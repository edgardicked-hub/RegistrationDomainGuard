<?php

namespace RegistrationDomainGuard\Middlewares;

use RegistrationDomainGuard\Services\RegistrationGuard;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Middleware;

class RegistrationDomainMiddleware extends Middleware
{
    public function before(Request $request)
    {
        /** @var RegistrationGuard $guard */
        $guard = pluginApp(RegistrationGuard::class);
        $guard->enforce($request);
    }

    public function after(Request $request, Response $response): Response
    {
        return $response;
    }
}
