<?php

namespace App\Http\Controllers\Api\V1;

use App\Apis\Ecom\EcomApi;
use App\Enums\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Apis\Gatekeeper\AuthApi;

class AuthController extends Controller
{
    protected $auth;

    const CUSTOMER_NOT_FOUND = 'USER_NOT_FOUND';
//    const USER_NOT_CREATED       = 'USER_NOT_CREATED';
//    const INVALID_MOBILE_NUMBER = 'INVALID_MOBILE_NUMBER';
//    const SAME_PASSWORD         = "SAME_PASSWORD";
//    const NOT_SAME_PASSWORD     = "NOT_SAME_PASSWORD";
//    const ADDRESS_NOT_FOUND     = "ADDRESS_NOT_FOUND";
    const AUTHORIZATION_HEADER_NOT_FOUND = "AUTHORIZATION_HEADER_NOT_FOUND";

    public function __construct(AuthApi $authApi)
    {
        $this->auth = $authApi;
    }

    /**
     * Get a JWT via given credentials.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $authHeader = $request->header('authorization', null);

        if (blank($authHeader)) {
            debug_log("Authorization header not found !", $request->all(), 'error');
            throw new UnauthorizedException(self::AUTHORIZATION_HEADER_NOT_FOUND, 401);
        }

        $data = [
            'token' => $authHeader
        ];

        $customer = app(EcomApi::class)->validateToken($data);
        if (blank($customer)) {
            debug_log("Token expired or customer not found !", $request->all(), 'error');
            throw new UnauthorizedException(self::CUSTOMER_NOT_FOUND, 410);
        }

        $customer = Customer::updateOrCreate(['auth_id' => $customer['auth_user_id']], [
            'name' => $customer['name'],
            'email' => $customer['email'],
            'mobile' => $customer['mobile'],
            'auth_id' => $customer['auth_user_id'],
            'ecom_area_id' => $customer['area_id'],
            'ecom_location_id' => $customer['location_id'],
            'shop_name' => $customer['shop_name'] ?? '',
        ]);

        if (!$customer) {
            debug_log("Kaiju customer not found !", $request->all(), 'error');
            throw new UnauthorizedException(self::CUSTOMER_NOT_FOUND, 410);
        }

        return $this->respondWithToken($customer);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return api(auth()->user())->success("Logged-in user");
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return api()->success(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param User $user
     * @param string $guard
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($user, $guard = 'retailer')
    {
        $data = [
            'access_token' => auth($guard)->login($user),
            'token_type' => 'bearer',
            'user' => $user,
        ];

        $ttl = JWTAuth::factory()->getTTL();

        if (!is_null($ttl)) {
            $data['expires_in'] = $ttl * 60;
        }

        return api($data)->success("Logged-in user");
    }

}
