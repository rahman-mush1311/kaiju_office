<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DistributorStatus;
use App\Enums\Role;
use App\Enums\SRStatus;
use App\Models\Distributor;
use App\Models\SalesRepresentative;
use App\Models\User;
use App\Presenters\DistributorPresenter;
use App\Presenters\SalesRepresentativePresenter;
use App\Presenters\UserPresenter;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class DistributorAuthController extends AuthController
{
    const DISTRIBUTOR_NOT_FOUND = 'DISTRIBUTOR_NOT_FOUND';
    const SR_NOT_FOUND = 'SR_NOT_FOUND';
    const MISMATCH_PASSWORD     = 'MISMATCH_PASSWORD';

    /**
     * Get a JWT via given credentials.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $inputEmail = $request->get('email');
        $email = is_valid_mobile_number($inputEmail) ? $inputEmail . '@deligram.com' : $inputEmail;

        $user = User::where('email', $email)->first();

        if (blank($user) || (!in_array(Role::DISTRIBUTOR, $user->roles) && !in_array(Role::SALES_REPRESENTATIVE, $user->roles))) {
            debug_log("User not found !", $request->all(), 'error');
            return api()->details("User not found!")->fails(self::DISTRIBUTOR_NOT_FOUND, Response::HTTP_GONE);
        }

        if ( \Hash::check($request->get('password'), $user->password) ) {

            if (in_array(Role::DISTRIBUTOR, $user->roles)) {
                $loggedInUser = Distributor::where('user_id', $user->id)
                    ->where('status', DistributorStatus::ACTIVE)
                    ->first();

                if (blank($loggedInUser)) {
                    return api()->details("User not found!")->fails(self::DISTRIBUTOR_NOT_FOUND, Response::HTTP_GONE);
                }

                $loggedInUser->name = trans_table_column($loggedInUser->name);
                $loggedInUser->role = Role::DISTRIBUTOR;
                return $this->respondWithAppToken($loggedInUser, 'distributor');
            } elseif(in_array(Role::SALES_REPRESENTATIVE, $user->roles)) {
                $loggedInUser = SalesRepresentative::with(['user'])
                    ->where('status', SRStatus::ACTIVE)
                    ->where('user_id', $user->id)->first();

                if (blank($loggedInUser)) {
                    return api()->details("User not found!")->fails(self::SR_NOT_FOUND, Response::HTTP_GONE);
                }

                $loggedInUser->name = $user->name;
                $loggedInUser->role = Role::SALES_REPRESENTATIVE;
                return $this->respondWithAppToken($loggedInUser, 'sr');
            }
        }

        return api()->details("Login credentials mismatch!")->fails(self::MISMATCH_PASSWORD, Response::HTTP_GONE);
    }

    private function respondWithAppToken($user, $guard = 'retailer')
    {
        $data = [
            'access_token' => auth($guard)->login($user),
            'token_type' => 'bearer',
        ];

        if ($guard == 'distributor') {
            $data['user'] = ( new DistributorPresenter($user->toArray()) )->get();
        } elseif ($guard == 'sr') {
            $data['user'] = ( new SalesRepresentativePresenter($user->toArray()) )->get();
        }

        $ttl = JWTAuth::factory()->getTTL();

        if (!is_null($ttl)) {
            $data['expires_in'] = $ttl * 60;
        }

        return api($data)->success("Logged-in user");
    }

}
