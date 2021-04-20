<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationService extends BaseService
{
    public function all(Request $request)
    {
        $query = Location::query();
        if($search = $request->get('search')) {
            $query->where(function($query) use($search) {
                $query->where(\DB::raw('LOWER(name_en)'), 'like', '%'. strtolower($search) .'%');
            });
        }

        return $query->paginate(10);
    }

    public function create(Array $data)
    {
        $location = app(Location::class);
        $location = $location->create($data);

        return $location;
    }

    public function getById($id)
    {
        return Location::find($id);
    }

    public function update($id, $data)
    {
        $location = Location::findOrFail($id);
        $location = tap($location)->update($data);

        return $location;
    }

    public function delete($id)
    {
        return Location::destroy($id);
    }
}
