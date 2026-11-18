<?php

use RegistrationDomainGuard\Services\RegistrationRequest;

require_once __DIR__ . '/../src/Services/RegistrationRequest.php';

$request = new RegistrationRequest();
$cases = [
    ['POST', '/de/rest/io/customer/', true],
    ['POST', '/en/rest/io/customer?source=shop', true],
    ['POST', '/pt-BR/rest/v1/io/customer', true],
    ['POST', '/de/rest/b2b/customer', true],
    ['POST', '/rest/io/customer', true],
    ['POST', '/rest/v1/b2b/customer', true],
    ['POST', '/io/customer', true],
    ['POST', '/b2b/customer', true],
    ['GET', '/de/rest/io/customer/', false],
    ['PUT', '/de/rest/io/customer/', false],
    ['POST', '/de/rest/io/customer/login', false],
    ['POST', '/de/rest/io/customer/password_reset', false],
    ['POST', '/de/rest/io/guest', false],
    ['POST', '/de/rest/io/checkout', false],
    ['POST', '/de/rest/customers', false],
    ['POST', '/de/io/customer', false],
    ['POST', '/other/rest/io/customer', false],
];

foreach ($cases as [$method, $uri, $expected]) {
    if ($request->isRegistration($method, $uri) !== $expected) {
        throw new RuntimeException(sprintf(
            'Expected %s %s to %s a registration request.',
            $method,
            $uri,
            $expected ? 'match' : 'skip'
        ));
    }
}

printf("Passed %d registration route checks.\n", count($cases));
