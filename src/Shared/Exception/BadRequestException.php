<?php

declare(strict_types=1);

namespace App\Shared\Exception;

final class BadRequestException extends AppException
{
    public function __construct(string $message = 'Bad Request', ?\Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
