@extends('layouts.master')

@section('title', 'Edit Product')

@section('heading', 'Edit Product')

@section('heading_buttons')
    <a href="{{route('product.create')}}" class="btn btn-primary">Create Product</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('product.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('product.update', [$product->id])}}" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name_en" value="{{$product->name_en}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name_bn" value="{{$product->name_bn}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>MRP</label>
                        <input type="text" class="form-control" name="mrp" value="{{$product->mrp}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>List Price</label>
                        <input type="text" class="form-control" name="trade_price" value="{{$product->trade_price}}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" id="" class="form-control">
                            @foreach($statuses as $key => $item)
                                <option {{$key == $product->status ? 'selected' : ''}} value="{{$key}}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Brands</label>
                        <select class="form-control select2" multiple="" name="brands[]">
                            @php
                                $ProductBrands = $product->brands->pluck('id')->toArray();
                            @endphp
                            @foreach($brands as $brand)
                                <option value="{{$brand->id}}" {{in_array($brand->id, $ProductBrands) ? 'selected' : ''}} >{{$brand->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Short Description</label>
                    <textarea name="short_description" id="" cols="30" rows="2" class="form-control">{{$product->short_description}}</textarea>
                </div>
                <div class="form-group">
                    <label for="">Long Description</label>
                    <textarea name="long_description" id="" cols="30" rows="10" class="form-control">{{$product->long_description}}</textarea>
                </div>
                <div class="form-group">
                    <label for="">Photo</label><br/>
                    <img src="{{$product->image}}" alt="" height="100" width="100">
                    <input type="file" name="image" id="" class="form-control">
                </div>

                @csrf
                @method('put')

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
