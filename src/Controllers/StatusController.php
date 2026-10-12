<?php

namespace RegistrationDomainGuard\Controllers;

use RegistrationDomainGuard\Services\DomainPolicy;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Response;

class StatusController extends Controller
{
    public function show(ConfigRepository $config, Response $response): Response
    {
        /** @var DomainPolicy $policy */
        $policy = pluginApp(DomainPolicy::class);
        $domains = $policy->normalizeDomains($config->get('RegistrationDomainGuard.allowedDomains', ''));
        return $response->make(json_encode([
            'plugin' => 'RegistrationDomainGuard',
            'version' => '1.1.2',
            'active' => true,
            'configured' => count($domains) > 0,
            'domainCount' => count($domains)
        ]), 200, [
            'Content-Type' => 'application/json; charset=utf-8',
            'Cache-Control' => 'no-store, private'
        ]);
    }
}
