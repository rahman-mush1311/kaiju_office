<?php


namespace App\Http\Controllers;


use App\Enums\ProductStatus;
use App\Filters\BrandFilter;
use App\Http\Requests\BrandRequest;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request, BrandFilter $filter)
    {
        $brands = Brand::filter($filter)->paginate();
        $input = $request->all();
        return view('brands.index', compact('brands', 'input'));
    }


    public function create()
    {
        return view('brands.create');
    }

    public function store(BrandRequest $request)
    {
        $data = [];

        if ($request->hasFile('image')) {
            $image = md5(Str::random(8) . time()) . '.' . $request->file('image')
                    ->getClientOriginalExtension();
            $request->file('image')->storeAs('kaiju', $image);
            $data['image'] = 'kaiju/' . $image;
        }

        $data['name'] = [
            'en' => $request->input('name_en'),
            'bn' => $request->input('name_bn'),
        ];

        $data = array_merge($data, $request->except('image', 'name_en', 'name_bn'));

        $brand = new Brand();
        $brand->fill($data);
        if ($brand->save()) {
            return redirect()->route('brands.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Brand!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Brand Creation Failed!']);
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('brands.edit', compact( 'brand'));
    }

    public function update($id, BrandRequest $request)
    {
        $data = [];

        if ($request->hasFile('image')) {
            $image = md5(Str::random(8) . time()) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('flash', $image);
            $data['image'] = 'flash/' . $image;
        }


        $data['name'] = [
            'en' => $request->input('name_en'),
            'bn' => $request->input('name_bn'),
        ];


        $data = array_merge($data, $request->except('image', 'name_en', 'name_bn'));


        $brand = Brand::findOrFail($id);
        $brand->fill($data);
        if ($brand->save()) {
            return redirect()->route('brands.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Brand!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Brand Update Failed!']);
    }
}
