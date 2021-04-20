<?php

namespace App\Console\Commands;

use App\Apis\Ecom\EcomApi;
use App\Models\Area;
use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncArea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:area';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync area information with ecommerce';

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
        Log::info("Area sync started");

        $this->syncAreas();

        return 0;
    }

    private function syncAreas($page = null)
    {
        $resp = app(EcomApi::class)->getAreas(['page' => $page]);
        if ($resp) {
            $currentPage = data_get($resp, 'current_page');
            $areas = data_get($resp, 'data', []);

            foreach ($areas as $area) {

                $location = Location::where("ecom_location_id", data_get($area, "location_id"))->first();

                if (blank($location)) {
                    continue;
                }

                $data = [
                    "name" => data_get($area, "name"),
                    "lat" => data_get($area, "lat"),
                    "long" => data_get($area, "long"),
                    "location_id" => data_get($location, "id"),
                    "ecom_area_id" => data_get($area, "id"),
                ];

                $existingArea = Area::where('ecom_area_id', data_get($area, "id"))->first();
                if (!blank($existingArea)) {
                    $existingArea->fill($data);
                    $existingArea->save();
                } else {
                    $newArea = new Area();
                    $newArea->fill($data);
                    $newArea->save();
                }
            }

            if (blank($areas)) {
                return;
            } else {
                $this->syncAreas(($currentPage+1));
            }
        }
    }
}
