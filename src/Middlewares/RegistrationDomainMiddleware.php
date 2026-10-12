<?php

namespace RegistrationDomainGuard\Middlewares;

use RegistrationDomainGuard\Services\DomainPolicy;
use RegistrationDomainGuard\Services\RegistrationRequest;
use RegistrationDomainGuard\Services\RejectionResponse;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Middleware;

class RegistrationDomainMiddleware extends Middleware
{
    public function before(Request $request)
    {
        /** @var RegistrationRequest $registrationRequest */
        $registrationRequest = pluginApp(RegistrationRequest::class);
        if (!$registrationRequest->isRegistration($request->getMethod(), $request->getRequestUri())) {
            return;
        }

        /** @var ConfigRepository $config */
        $config = pluginApp(ConfigRepository::class);
        /** @var DomainPolicy $policy */
        $policy = pluginApp(DomainPolicy::class);
        $domains = $policy->normalizeDomains($config->get(
            'RegistrationDomainGuard.allowedDomains',
            ''
        ));

        /** @var RejectionResponse $rejection */
        $rejection = pluginApp(RejectionResponse::class);
        if (count($domains) === 0) {
            $rejection->send(
                'Die Registrierung ist derzeit nicht verfügbar. Bitte wenden Sie sich an den Shopbetreiber.',
                503
            );
            return;
        }

        // Read exactly the same contact object as IO CustomerResource and B2BShop.
        // Never inspect the billing email in place of the login email.
        if (!$policy->allows($request->get('contact', null), $domains)) {
            $rejection->send(
                'Bitte verwenden Sie eine E-Mail-Adresse mit einer der erlaubten Endungen: @'
                    . implode(', @', $domains) . '.',
                422
            );
        }
    }

    public function after(Request $request, Response $response): Response
    {
        return $response;
    }
}
