<?php

namespace RegistrationDomainGuard\Providers;

use RegistrationDomainGuard\Middlewares\RegistrationDomainMiddleware;
use RegistrationDomainGuard\Services\RegistrationGuard;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\ServiceProvider;

class RegistrationGuardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->addGlobalMiddleware(RegistrationDomainMiddleware::class);
        $this->getApplication()->register(RegistrationGuardRouteServiceProvider::class);
    }

    public function boot(Request $request)
    {
        /** @var RegistrationGuard $guard */
        $guard = pluginApp(RegistrationGuard::class);
        $guard->enforce($request);
    }
}
