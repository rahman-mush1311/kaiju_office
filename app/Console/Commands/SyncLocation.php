<?php

namespace App\Console\Commands;

use App\Apis\Ecom\EcomApi;
use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync location information with ecommerce';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Location sync started");

        $this->syncLocations();

        return 0;
    }

    private function syncLocations($page = null)
    {
        $resp = app(EcomApi::class)->getLocations(['page' => $page]);
        if ($resp) {
            $currentPage = data_get($resp, 'current_page');
            $locations = data_get($resp, 'data', []);

            foreach ($locations as $location) {
                $data = [
                    "name" => data_get($location, "name"),
                    "ecom_location_id" => data_get($location, "id"),
                ];

                $existingLocation = Location::where('ecom_location_id', data_get($location, "id"))->first();
                if (!blank($existingLocation)) {
                    $existingLocation->fill($data);
                    $existingLocation->save();
                } else {
                    $newLocation = new Location();
                    $newLocation->fill($data);
                    $newLocation->save();
                }
            }

            if (blank($locations)) {
                return;
            } else {
                $this->syncLocations(($currentPage+1));
            }
        }
    }
}
