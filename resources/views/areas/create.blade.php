@extends('layouts.master')

@section('title', 'Create Area')

@section('heading', 'Create Area')

@section('breadcrumbs', Breadcrumbs::render('location.create'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('area.store')}}" method="post" enctype="multipart/form-data">
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

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="lat">Latitude</label>
                        <input type="text" id="lat" class="form-control" name="lat" value="{{ old('lat') }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="long">Longitude</label>
                        <input type="text" id="long" class="form-control" name="long" value="{{ old('long') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <select id="location" name="location_id" class="form-control">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
