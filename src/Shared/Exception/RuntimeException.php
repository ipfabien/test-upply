<?php

declare(strict_types=1);

namespace App\Shared\Exception;

final class RuntimeException extends AppException
{
    public function __construct(string $message = 'Runtime error', ?\Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}
