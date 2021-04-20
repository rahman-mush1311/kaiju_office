@extends('layouts.master')

@section('title', 'Create Delivery Charge Rule')

@section('heading', 'Create Delivery Charge Rule')

@section('breadcrumbs', Breadcrumbs::render('delivery.charge.rule.create'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('delivery.charge.rules.store')}}" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Minimum Basket Size</label>
                        <input type="text" class="form-control" name="min_basket_size" value="{{ old('min_basket_size') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Maximum Basket Size</label>
                        <input type="text" class="form-control" name="max_basket_size" value="{{ old('max_basket_size') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Delivery Charge</label>
                        <input type="text" class="form-control" name="delivery_charge" value="{{ old('delivery_charge') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="{{ \App\Enums\DeliveryChargeRuleStatus::ACTIVE }}">Active</option>
                            <option value="{{ \App\Enums\DeliveryChargeRuleStatus::INACTIVE }}">Inactive</option>
                        </select>
                    </div>
                </div>

                @csrf

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
