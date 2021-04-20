<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Location;
use App\Presenters\AreaPresenter;
use App\Presenters\LocationPresenter;

class LocationController extends Controller
{

    public function locations()
    {
        $locations = Location::all();

        return (new LocationPresenter($locations))();
    }

    public function locationAreas($id)
    {
        $areas = Area::join('agents', 'agents.area_id', '=', 'areas.id')
        ->where('areas.location_id', $id)
        ->where('agents.status', 1)
        ->select('areas.*')
        ->groupBy('areas.id')
        ->orderBy('areas.name_en')
        ->get();

        return (new AreaPresenter($areas))();
    }

}
