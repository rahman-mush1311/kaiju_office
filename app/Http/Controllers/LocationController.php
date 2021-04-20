<?php


namespace App\Http\Controllers;


use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use App\Filters\LocationFilter;
use Illuminate\Support\Facades\Artisan;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = app(LocationService::class)->all($request);
        $input = $request->all();

        return view('locations.index', compact('locations', 'input'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(LocationRequest $request)
    {
        try {
            app(LocationService::class)->create($request->only(app(Location::class)->getFillable()));

            return redirect()->route('location.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Location!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Location Creation Failed!']);
        }
    }

    public function edit($id)
    {
        $location = app(LocationService::class)->getById($id);

        return view('locations.edit', compact('location'));
    }

    public function update($id, LocationRequest $request)
    {
        try {
            $data = $request->only(app(Location::class)->getFillable());
            app(LocationService::class)->update($id, $data);

            return redirect()->route('location.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Location!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Location Update Failed!']);
        }
    }

    public function locationListSelect2(LocationFilter $filter)
    {
        $locations = Location::filter($filter)->limit(10)->get();
        return $locations->transform(function($item, $key){
            return [
                'id' => $item->id,
                'text' => $item->name,
            ];
        });
    }

    public function syncLocation()
    {
        Artisan::call('sync:location');
        return redirect()->route('location.index')->with(['_status' => 'success', '_msg' => 'Location sync in progress!']);
    }
}
