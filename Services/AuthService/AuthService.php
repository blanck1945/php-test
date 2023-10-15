<?php

namespace Services\AuthService;

use Core\Injectable\Injectable;

class AuthService extends Injectable
{
    public function create_user($user)
    {
        return [
            // 'tenant_name' => $body->tenant_name,
            // 'tenant_email' => $body->tenant_email
        ];
    }
}
