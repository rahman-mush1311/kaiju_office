<?php

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Distributor;
use App\Models\Customer;
class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /* $order = new Order([
            'id' => 1,
            'customer_id' => 1,
            'customer_mobile' => '01672000098',
            'distributor_id' => 1,
            'tracking_id' => 'DG202121',
            'misc'=>json_encode(["customer_name"=>"deligram_dg"])

        ]);

       $order->save();*/

      /*  $customer = new Customer([
            'id' => 1,
            'name' => 'Gus Fring',
            'email'=> 'gus@deligram.com',
            'mobile' => '01672000098',
            'shop_name'=>'Los Polos'
        ]);*/

      // $customer->save();
        $distributor = new Distributor([
            'id' => 1,
            'user_id' => '8d0894ae-72a4-46f8-8b97-f0df03190deb',
            'name'=>'Walter White',
            'email'=> 'dd@deligram.com',
            'mobile' => '01672000091',

        ]);
        $distributor->save();
    }
}
