<?php

namespace RegistrationDomainGuard\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\ApiRouter;
use Plenty\Plugin\Routing\Router;

class RegistrationGuardRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router, ApiRouter $api)
    {
        $api->version(['v1'], ['namespace' => 'RegistrationDomainGuard\Controllers'], function (ApiRouter $api) {
            $api->get('registration-domain-guard/status', 'StatusController@show');
        });
    }
}
