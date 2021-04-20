@extends('layouts.master')

@section('title', 'Create Category')

@section('heading', 'Create Category')

@section('breadcrumbs', Breadcrumbs::render('brands.create'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('brands.store')}}" method="post" enctype="multipart/form-data">
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
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ old('description') }}</textarea>
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
