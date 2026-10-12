<?php

namespace RegistrationDomainGuard\Services;

use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Http\Request;

class RegistrationGuard
{
    /** Run before controller dispatch, including REST requests outside the web middleware stack. */
    public function enforce(Request $request): void
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
        $domains = $policy->normalizeDomains($config->get('RegistrationDomainGuard.allowedDomains', ''));

        /** @var RejectionResponse $rejection */
        $rejection = pluginApp(RejectionResponse::class);
        if (count($domains) === 0) {
            $rejection->send(
                'Die Registrierung ist derzeit nicht verfügbar. Bitte wenden Sie sich an den Shopbetreiber.',
                503
            );
            return;
        }

        if (!$policy->allows($request->get('contact', null), $domains)) {
            $rejection->send(
                'Bitte verwenden Sie eine E-Mail-Adresse mit einer der erlaubten Endungen: @'
                    . implode(', @', $domains) . '.',
                422
            );
        }
    }
}
