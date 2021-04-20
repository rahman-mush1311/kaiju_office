<?php


namespace App\Http\Controllers;

use App\Filters\AreaFilter;
use App\Http\Requests\AreaRequest;
use App\Models\Area;
use App\Models\Location;
use App\Services\AreaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = app(AreaService::class)->all($request);
        $locations = Location::select('id', 'name_en')->get();
        $input = $request->all();

        return view('areas.index', compact('areas', 'locations', 'input'));
    }

    public function create()
    {
        $locations = Location::select('id', 'name')->get();

        return view('areas.create', compact('locations'));
    }

    public function store(AreaRequest $request)
    {
        try {
            app(AreaService::class)->create($request->all());

            return redirect()->route('area.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Area!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Area Creation Failed!']);
        }
    }

    public function edit($id)
    {
        $area = app(AreaService::class)->getById($id);
        $locations = Location::select('id', 'name')->get();

        return view('areas.edit', compact('area', 'locations'));
    }

    public function update($id, AreaRequest $request)
    {
        try {
            $data = $request->only(app(Area::class)->getFillable());
            app(AreaService::class)->update($id, $data);

            return redirect()->route('area.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Area!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Area Update Failed!']);
        }
    }

    public function searchArea(Request $request)
    {
        $term = $request->get('search');
        $query = Area::whereRaw('lower(name_en) like \'%'.strtolower($term).'%\'');

        if ($locations = $request->get('locations')) {
            $query->whereIn('location_id', $locations);
        }

        $areas = $query->limit(10)->get();

        return $areas->transform(function($item, $key){
            return [
                'id' => $item->id,
                'text' => str_replace('"', '', $item->name_en),
            ];
        });
    }

    public function areaListSelect2($locationId, AreaFilter $filter)
    {
        $areas = Area::where('location_id',$locationId)->filter($filter)->limit(10)->get();
        return $areas->transform(function($item, $key){
            return [
                'id' => $item->id,
                'text' => $item->name,
            ];
        });
    }

    public function syncArea()
    {
        Artisan::call('sync:area');
        return redirect()->route('area.index')->with(['_status' => 'success', '_msg' => 'Area sync in progress!']);
    }
}
