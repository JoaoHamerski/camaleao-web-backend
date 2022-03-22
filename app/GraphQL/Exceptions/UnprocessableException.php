<?php

namespace App\GraphQL\Exceptions;

use Exception;
use Nuwave\Lighthouse\Exceptions\RendersErrorsExtensions;

class UnprocessableException extends Exception implements RendersErrorsExtensions
{
    protected $reason;

    public function __construct(string $message, string $reason)
    {
        parent::__construct($message);

        $this->reason = $reason;
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'unprocessable';
    }

    public function extensionsContent(): array
    {
        return [
            'reason' => $this->reason
        ];
    }
}
