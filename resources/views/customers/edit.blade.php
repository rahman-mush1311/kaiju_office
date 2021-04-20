@extends('layouts.master')

@section('title', 'Edit Retailer')

@section('heading', 'Edit Retailer')

@section('heading_buttons')
    <a href="{{route('customers.index')}}" class="btn btn-primary">All Retailer</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('customers.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('customers.update', [$customer->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $customer->name }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" value="{{ $customer->email }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Mobile</label>
                        <input type="text" class="form-control" name="mobile" value="{{ $customer->mobile ?? '' }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            @foreach(trans('customer.status') as $key => $status)
                                <option value="{{ $key }}" @if($key == $customer->status) selected @endif>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @method('put')

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection

