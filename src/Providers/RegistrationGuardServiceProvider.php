<?php

namespace RegistrationDomainGuard\Providers;

use RegistrationDomainGuard\Middlewares\RegistrationDomainMiddleware;
use Plenty\Plugin\ServiceProvider;

class RegistrationGuardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->addGlobalMiddleware(RegistrationDomainMiddleware::class);
    }
}
