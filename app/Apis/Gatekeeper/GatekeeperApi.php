<?php

namespace App\Apis\Gatekeeper;

use App\Apis\BaseApi;

abstract class GatekeeperApi extends BaseApi
{
    public $prefix = 'api/v1';
    public $httpExceptions = [];
    public $options = [
        'http_errors' => false
    ];

    public function baseUrl()
    {
        return env('GATEKEEPER_HOST', 'http://localhost');
    }

    public function setDefaultHeaders()
    {
        return [
            'X-DG-Authorization' => 'Service ' . $this->getClientToken()
        ];
    }

    protected function getClientToken()
    {
        $tokenString = env('GATEKEEPER_CLIENT_ID', '') . ':' . env('GATEKEEPER_CLIENT_SECRET', '');

        return base64_encode($tokenString);
    }

}
