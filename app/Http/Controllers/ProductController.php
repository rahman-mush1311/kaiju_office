<?php


namespace App\Http\Controllers;


use App\Exports\ProductExport;
use App\Filters\ProductFilter;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductImport;
use App\Models\Brand;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request, ProductFilter $filter)
    {
        $products = Product::filter($filter)->paginate();
        $input = $request->all();
        $brands = Brand::all();
        return view('products.index', compact('products', 'input', 'brands'));
    }


    public function create()
    {
        $statuses = trans('product.status');
        $brands = Brand::all();
        return view('products.create', compact('statuses', 'brands'));
    }

    public function store(ProductRequest $request)
    {
        $data = [];

        $image = null;
        if($request->file('image')) {
            $image = md5(Str::random(8) . time()) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('kaiju', $image);
        }

        $data['name'] = [
            'en' => $request->input('name_en'),
            'bn' => $request->input('name_bn'),
        ];

        if($image) {
            $data['image'] = 'kaiju/' . $image;
        }

        $data = array_merge($data, $request->except('image', 'name_en', 'name_bn'));
        $data['trade_price'] = $request->get('trade_price') ?? '0.00';

        $product = new Product();
        $product->fill($data);
        if ($product->save()) {
            $product->brands()->sync($request->input('brands'));
            return redirect()->route('product.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Product!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Product Creation Failed!']);
    }

    public function edit($id)
    {
        $statuses = trans('product.status');
        $product = Product::with('brands')->findOrFail($id);
        $brands = Brand::all();

        return view('products.edit', compact('statuses', 'product', 'brands'));
    }

    public function update($id, ProductRequest $request)
    {
        $data = [];

        if ($request->hasFile('image')) {
            $image = md5(Str::random(8) . time()) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('kaiju', $image);
            $data['image'] = 'kaiju/' . $image;
        }


        $data['name'] = [
            'en' => $request->input('name_en'),
            'bn' => $request->input('name_bn'),
        ];

        $data = array_merge($data, $request->except('image', 'name_en', 'name_bn'));
        $data['trade_price'] = $request->get('trade_price') ?? '0.00';

        $product = Product::findOrFail($id);
        $product->fill($data);
        if ($product->save()) {
            $product->brands()->sync($request->input('brands'));
            return redirect()->route('product.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Product!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Product Update Failed!']);
    }

    public function searchProduct(Request $request)
    {
        $term = $request->get('search');
        $brands = $request->get('brands');
        $selectedProducts = $request->get('selected_products');

        $productsQuery = Product::whereHas('brands', function($q) use($brands){
            $q->whereIn('id', $brands);
        })
        ->whereRaw('lower(name_en) like \'%'.strtolower($term).'%\'');

        if (!blank($selectedProducts)) {
            $excludeIds = json_decode($selectedProducts);
            $productsQuery->whereNotIn('id', $excludeIds);
        }

        $products = $productsQuery->limit(10)->get();



        return $products->transform(function ($item, $key) {
            return [
                'id' => $item->id,
                'text' => $item->name_en . ' (' . $item->short_description . ')',
                'trade_price' => $item->trade_price
            ];
        });
    }

    public function distributorProducts(Request $request)
    {
        return response()->json(app(ProductService::class)->searchDistributorProduct($request));
    }

    public function exportImportView()
    {
        return view('products.export-import-product');
    }

    public function exportExcel(Request $request)
    {
        return (new ProductExport($request->all()))
            ->download('products@'. date('Y:m:d h:i:s') .'.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate(['products' => 'file|mimes:xlsx']);

        DB::beginTransaction();
        try {
            if(!$request->hasFile('products')) {
                throw ValidationException::withMessages(['Invalid File']);
            }

            Excel::import(new ProductImport(), $request->file('products'));
            DB::commit();

            return redirect()->back()->with([
                '_status' => 'success',
                '_msg' => 'Product imported successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            DB::rollBack();
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Product import failed! Please check your excel file is valid.']);
        }
    }
}
