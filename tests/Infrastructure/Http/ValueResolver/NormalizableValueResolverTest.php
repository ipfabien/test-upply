<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Http\ValueResolver;

use App\Infrastructure\Http\ValueResolver\NormalizableValueResolver;
use App\Shared\Exception\BadRequestException;
use App\Shared\Normalization\Normalizable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class NormalizableValueResolverTest extends TestCase
{
    public function testRejectsNonJsonContentType(): void
    {
        $resolver = new NormalizableValueResolver();
        $request = Request::create(
            uri: '/knight',
            method: 'POST',
            server: ['HTTP_CONTENT_TYPE' => 'text/plain'],
            content: '{"name":"Foo"}'
        );

        $meta = new ArgumentMetadata(
            'dto',
            DummyNormalizable::class,
            false,
            false,
            null
        );

        $this->expectException(BadRequestException::class);
        iterator_to_array($resolver->resolve($request, $meta));
    }

    public function testRejectsInvalidJsonPayload(): void
    {
        $resolver = new NormalizableValueResolver();
        $request = Request::create(
            uri: '/knight',
            method: 'POST',
            server: ['HTTP_CONTENT_TYPE' => 'application/json'],
            content: 'not-json'
        );

        $meta = new ArgumentMetadata(
            'dto',
            DummyNormalizable::class,
            false,
            false,
            null
        );

        $this->expectException(BadRequestException::class);
        iterator_to_array($resolver->resolve($request, $meta));
    }

    public function testDenormalizeInvalidArgumentBecomesBadRequest(): void
    {
        $resolver = new NormalizableValueResolver();
        $request = Request::create(
            uri: '/knight',
            method: 'POST',
            server: ['HTTP_CONTENT_TYPE' => 'application/json'],
            content: json_encode(['k' => 'v'])
        );

        $meta = new ArgumentMetadata(
            'dto',
            ThrowingNormalizable::class,
            false,
            false,
            null
        );

        $this->expectException(BadRequestException::class);
        iterator_to_array($resolver->resolve($request, $meta));
    }
}

final class DummyNormalizable implements Normalizable
{
    /**
     * @param array<string,mixed> $data
     */
    public static function denormalize(array $data): static
    {
        return new self();
    }

    /**
     * @return array<string,mixed>
     */
    public function normalize(): array
    {
        return [];
    }
}

final class ThrowingNormalizable implements Normalizable
{
    public static function denormalize(array $data): static
    {
        throw new \InvalidArgumentException('Invalid payload.');
    }

    public function normalize(): array
    {
        return [];
    }
}
