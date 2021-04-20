@extends('layouts.master')

@section('title', 'Edit Area')

@section('heading', 'Edit Area')

@section('heading_buttons')
    <a href="{{route('area.create')}}" class="btn btn-primary">Create Area</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('area.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('area.update', [$area->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name[en]" value="{{ str_replace('"', '', $area->name_en) }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name[bn]" value="{{ str_replace('"', '', $area->name_bn) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="lat">Latitude</label>
                        <input type="text" id="lat" class="form-control" name="lat" value="{{ $area->lat }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="long">Longitude</label>
                        <input type="text" id="long" class="form-control" name="long" value="{{ $area->long }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <select id="location" name="location_id" class="form-control">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @if($area->location_id == $location->id) selected @endif>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                @method('put')

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
