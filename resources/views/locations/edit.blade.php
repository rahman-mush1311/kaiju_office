@extends('layouts.master')

@section('title', 'Edit Location')

@section('heading', 'Edit Location')

@section('heading_buttons')
    <a href="{{route('location.create')}}" class="btn btn-primary">Create Location</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('location.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('location.update', [$location->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name[en]" value="{{ str_replace('"', '', $location->name_en) }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name[bn]" value="{{ str_replace('"', '', $location->name_bn) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="details">Description</label>
                    <textarea name="details" id="details" cols="30" rows="5" class="form-control" style="min-height: 80px;" >{{ $location->details }}</textarea>
                </div>
                @method('put')

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
