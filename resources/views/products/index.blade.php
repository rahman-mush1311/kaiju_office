@extends('layouts.master')

@section('title', 'Product List')

@section('heading', 'Product List')

@section('breadcrumbs', Breadcrumbs::render('product.index'))

@section('contents')
    <form action="{{route('product.index')}}">
    <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
        <div class="card card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Select Status</option>
                            @foreach(trans('product.status') as $key => $status)
                                <option value="{{ $key }}" @if(array_get($input, 'status') == $key) selected @endif>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="brand">Brand</label>
                        <select id="brand" name="brand" class="form-control">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{$brand->id}}" @if(array_get($input, 'brand') == $brand->id) selected @endif>
                                    {{str_replace('"', '', $brand->name_en)}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3" style="margin-top: 30px">
                        <button type="submit" class="btn btn-primary" style="margin-right: 5px">Apply Filters</button>
                        @if(!empty(array_filter($input)))
                            <a href="{{ route('product.index') }}" class="btn btn-light">Remove Filters</a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>All Products</h4>
            <div class="card-header-form">
                <div class="input-group">
{{--                    <a href="{{ route('product.export') }}" class="btn btn-primary" style="margin-right: 10px;">Export</a>--}}
                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ $input['search'] ?? '' }}">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="table-responsive card-list-table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                @php $index = $products->firstItem(); @endphp
                @foreach($products as $product)
                <tr>
                    <td scope="row" data-title="#">{{$index++}}</td>
                    <td data-title="Image">
                        <img src="{{$product->image}}" alt="{{$product->name}}" width="50" height="50">
                    </td>
                    <td data-title="Name">{{$product->name}} - {{$product->short_description}}</td>
                    <td data-title="Created At">{{$product->created_at}}</td>
                    <td data-title="Actions">
                        <a href="{{route('product.edit', [$product->id])}}" class="btn btn-sm btn-info">Edit</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="dataTables_info" id="table-1_info" role="status" aria-live="polite">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="float-right">
                        {{ $products->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection
