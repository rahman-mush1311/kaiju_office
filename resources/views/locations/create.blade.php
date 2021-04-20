@extends('layouts.master')

@section('title', 'Create Location')

@section('heading', 'Create Location')

@section('breadcrumbs', Breadcrumbs::render('location.create'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('location.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name[en]" value="{{ old('name.en') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name[bn]" value="{{ old('name.bn') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="details">Description</label>
                    <textarea name="details" id="details" cols="30" rows="10" class="form-control" style="min-height: 80px;" >{{ old('details') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
