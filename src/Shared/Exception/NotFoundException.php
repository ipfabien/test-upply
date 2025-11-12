<?php

declare(strict_types=1);

namespace App\Shared\Exception;

final class NotFoundException extends AppException
{
    public function __construct(string $message = 'Not Found', ?\Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
