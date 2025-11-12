<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Exception;

use App\Shared\Exception\AppException;
use App\Shared\Http\ApiEndpoint;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private ?LoggerInterface $logger = null)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();
        $controller = $event->getRequest()->attributes->get('_controller');
        $req = $event->getRequest();

        if ($this->isApiController($controller === false)) {
            return;
        }

        if ($e instanceof AppException) {
            $status = $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

            $this->logger?->warning('http.error.app_exception', [
                'method' => $req->getMethod(),
                'path' => $req->getPathInfo(),
                'status' => $status,
                'message' => $e->getMessage(),
                'exception' => $e::class,
                'id' => $req->attributes->get('id'),
            ]);

            $event->setResponse(
                new JsonResponse(
                    [
                        'code' => $status,
                        'message' => $e->getMessage(),
                    ],
                    $status,
                )
            );

            return;
        }

        $this->logger?->error('http.error.unexpected', [
            'method' => $req->getMethod(),
            'path' => $req->getPathInfo(),
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $e->getMessage(),
            'exception' => $e::class,
        ]);

        $event->setResponse(
            new JsonResponse(
                [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'An unexpected error occurred.',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            )
        );
    }

    /**
     * Determine if the current controller action is marked as an API endpoint.
     */
    private function isApiController(mixed $controller): bool
    {
        if (!\is_string($controller) || !str_contains($controller, '::')) {
            return false;
        }

        [$class, $method] = explode('::', $controller, 2);

        if (!class_exists($class)) {
            return false;
        }

        $refClass = new ReflectionClass($class);

        if (!empty($refClass->getAttributes(ApiEndpoint::class))) {
            return true;
        }

        if (!$refClass->hasMethod($method)) {
            return false;
        }

        $refMethod = new ReflectionMethod($class, $method);

        return !empty($refMethod->getAttributes(ApiEndpoint::class));
    }
}
