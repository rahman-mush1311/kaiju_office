<?php

namespace App\Apis\Ecom;

use App\Apis\BaseApi;
use Illuminate\Http\Response;

class EcomApi extends BaseApi
{
    protected $prefix = 'internal';
    protected $options = [
        'http_errors' => false,
    ];

    public function baseUrl()
    {
        return env('ECOM_HOST');
    }

    public function setDefaultHeaders()
    {
        $token = base64_encode(env('ECOM_CLIENT_ID') . ':' . env('ECOM_CLIENT_SECRET'));
        return [
            'X-DG-Authorization' => 'Service ' . $token ,
        ];
    }

    public function getCustomerById($id)
    {
        return $this->get('customers/' . $id);
    }

    public function validateToken( $data )
    {
        $user = $this->headers([
            'authorization' => $data['token']
        ])->post('validate/token');

        if ($user->getStatusCode() == Response::HTTP_OK) {
            $user = $user->query()->toArray();
            if ($user['code'] === Response::HTTP_OK) {
                return $user['data'];
            }
            return null;
        }
        return null;
    }

    public function sendPush($request)
    {
        return $this->json($request)->post('barta/push');
    }

    public function sendSms($request)
    {
        return $this->json($request)->post('barta/sms');
    }

    public function getLocations($request)
    {
        $res = $this->json($request)->get('locations');
        if ($res->getStatusCode() == Response::HTTP_OK) {
            return $res->query()->toArray();
        }
        return null;
    }

    public function getAreas($request)
    {
        $res = $this->json($request)->get('areas');
        if ($res->getStatusCode() == Response::HTTP_OK) {
            return $res->query()->toArray();
        }
        return null;
    }

    public function getAddress($addressOwner)
    {
        $res = $this->get('address/' . $addressOwner);
        if ($res->getStatusCode() == Response::HTTP_OK) {
            return $res->query()->toArray();
        }
        return null;
    }
}
