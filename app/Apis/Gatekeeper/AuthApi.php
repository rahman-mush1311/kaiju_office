<?php

namespace App\Apis\Gatekeeper;

class AuthApi extends GatekeeperApi
{
    public function validate($email, $password)
    {
        return $this
            ->json([
                'email' => $email,
                'password' => $password
            ])
            ->post('validate');
    }

    public function store($request)
    {
        return $this
            ->json($request)
            ->post('users');
    }

    public function update($id, $request)
    {
        return $this
            ->json($request)
            ->put('users/'.$id);
    }
}
