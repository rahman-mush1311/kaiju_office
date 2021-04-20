@extends('layouts.master')

@section('title', 'Edit Brand')

@section('heading', 'Edit Brand')

@section('breadcrumbs', Breadcrumbs::render('brands.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('brands.update', [$brand->id])}}" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name_en" value="{{ str_replace('"', '', $brand->name_en) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name_bn" value="{{ str_replace('"', '', $brand->name_bn) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Photo</label> @if($brand->image) <br> @endif
                        <img style="margin-bottom: 5px; clear: both;" src="{{$brand->image}}" alt="" height="80" width="80">
                        <input type="file" name="image" id="" class="form-control">
                    </div>
                    <div class="form-group col-md-6" @if($brand->image) style="margin-top: 80px;" @endif>
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="{{ \App\Enums\BrandStatus::ACTIVE }}">Active</option>
                            <option {{ $brand->status == \App\Enums\BrandStatus::INACTIVE ? 'selected' : '' }} value="{{ \App\Enums\BrandStatus::INACTIVE }}">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ $brand->description }}</textarea>
                </div>

                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
