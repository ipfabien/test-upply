<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ValueResolver;

use App\Shared\Exception\BadRequestException;
use App\Shared\Normalization\Normalizable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class NormalizableValueResolver implements ValueResolverInterface
{
    /**
     * @return iterable<Normalizable>
     * @throws BadRequestException When content type is invalid or payload is not valid JSON
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // Only handle arguments that implement Normalizable
        $type = $argument->getType();
        if (!\is_string($type) || !class_exists($type) || !is_subclass_of($type, Normalizable::class)) {
            return [];
        }

        $this->checkJsonContentType($request);

        $data = json_decode((string) $request->getContent(), true);

        if (!\is_array($data)) {
            // Missing/unknown content-type is tolerated if payload is valid JSON; otherwise it's a JSON issue
            throw new BadRequestException('Invalid JSON payload.');
        }

        $type = (string) $argument->getType();

        /** @var class-string<Normalizable> $type */
        try {
            $object = $type::denormalize($data);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestException($e->getMessage());
        }

        yield $object;
    }

    /**
     * Determine declared content type (support BrowserKit and raw server vars)
     *
     * @throws BadRequestException
     */
    private function checkJsonContentType(Request $request): void
    {
        $contentTypeAllowedValues = array_values(array_filter([
            strtolower((string) $request->headers->get('Content-Type')),
            strtolower((string) $request->server->get('HTTP_CONTENT_TYPE')),
            strtolower((string) $request->server->get('CONTENT_TYPE')),
        ], static fn ($v) => \is_string($v) && $v !== ''));

        $hasContentType = \count($contentTypeAllowedValues) > 0;
        $hasJsonDeclared = false;

        foreach ($contentTypeAllowedValues as $contentType) {
            if (str_contains($contentType, 'json')) {
                $hasJsonDeclared = true;

                break;
            }
        }
        $isExplicitNonJson = $hasContentType && $hasJsonDeclared === false;

        // If a content type is explicitly provided and it's not JSON, reject immediately
        if ($isExplicitNonJson) {
            throw new BadRequestException('Invalid content type.');
        }
    }
}
