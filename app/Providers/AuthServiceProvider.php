<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use App\Enums\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();

        // check for admin
        $gate->define("isAdmin", function($user){
            return in_array(Role::ADMIN, $user->roles);
        });

        // check for distributor
        $gate->define("isDistributor", function($user){
            return in_array(Role::DISTRIBUTOR, $user->roles);
        });

        // check for sales officer
        $gate->define("isSalesRepresentative", function($user){
            return in_array(Role::SALES_REPRESENTATIVE, $user->roles);
        });
    }
}
