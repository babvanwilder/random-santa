<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class UserNotVerifiedException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return 'Account is not verified.';
    }
}
