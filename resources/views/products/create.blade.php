@extends('layouts.master')

@section('title', 'Create Product')

@section('heading', 'Create Product')

@section('breadcrumbs', Breadcrumbs::render('product.create'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('product.store')}}" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name_en" value="{{ old('name_en') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name_bn" value="{{ old('name_bn') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>MRP</label>
                        <input type="text" class="form-control" name="mrp" value="{{ old('mrp') }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>List Price</label>
                        <input type="text" class="form-control" name="trade_price" value="{{ old('trade_price') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" id="" class="form-control">
                            @foreach($statuses as $key => $item)
                                <option value="{{$key}}" {{old('status') == $key ? 'selected':''}}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Brands</label>
                        <select class="form-control select2" multiple="" name="brands[]">
                            @foreach($brands as $brand)
                                <option value="{{$brand->id}}" {{in_array($brand->id, old('brands', [])) ? 'selected':''}}>{{$brand->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="form-group">
                    <label for="">Short Description</label>
                    <textarea name="short_description" id="" cols="30" rows="2" class="form-control">{{ old('short_description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="">Long Description</label>
                    <textarea name="long_description" id="" cols="30" rows="10" class="form-control">{{ old('long_description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="">Photo</label>
                    <input type="file" name="image" id="" class="form-control">
                </div>

                @csrf

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
