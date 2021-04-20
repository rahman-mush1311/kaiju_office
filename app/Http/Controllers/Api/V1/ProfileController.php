<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\SalesRepresentative;
use App\Presenters\DistributorPresenter;
use App\Presenters\SalesRepresentativePresenter;

class ProfileController extends Controller
{
    public function loggedUserProfile()
    {
        $data = [];
        if (!auth('distributor')->guest()) {
            $distributorId =  auth('distributor')->user()->id;
            $loggedInUser = Distributor::find($distributorId);
            $loggedInUser->name = trans_table_column($loggedInUser->name);
            $loggedInUser->role = Role::DISTRIBUTOR;
            $data = ( new DistributorPresenter($loggedInUser->toArray()) )->get();
        } elseif (!auth('sr')->guest()) {
            $srId = auth('sr')->user()->id;
            $loggedInUser = SalesRepresentative::with(['user'])->find($srId);
            $loggedInUser->role = Role::SALES_REPRESENTATIVE;
            $data = ( new SalesRepresentativePresenter($loggedInUser->toArray()) )->get();
        }

        return api($data)->details('Profile fetched successfully')
            ->success('USER_PROFILE');
    }

}
