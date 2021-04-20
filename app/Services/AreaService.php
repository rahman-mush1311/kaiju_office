<?php

namespace App\Services;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use function GuzzleHttp\Psr7\str;

class AreaService extends BaseService
{
    public function all(Request $request)
    {
        $query = Area::query()->with('location');

        if($search = $request->get('search')) {
            $query->where(function($query) use($search) {
                $query->where(\DB::raw('LOWER(name_en)'), 'like', '%'. strtolower($search) .'%');
            });
        }

        if($location = $request->get('location_id')) {
            $query->where(function($query) use($location) {
                $query->where('location_id', '=', $location);
            });
        }

        return $query->paginate(10);
    }

    public function create(Array $data)
    {
        $area = new Area();
        $area->fill(Arr::only($data, app(Area::class)->getFillable()));


        if ($area->save()) {
            return $area;
        }

        return false;
    }

    public function getById($id)
    {
        return Area::find($id);
    }

    public function update($id, $data)
    {
        $area = Area::findOrFail($id);
        $area = tap($area)->update($data);

        return $area;
    }

    public function delete($id)
    {
        return Area::destroy($id);
    }
}
