@extends('layouts.master')

@section('title', 'Edit Delivery Charge Rule')

@section('heading', 'Edit Delivery Charge Rule')

@section('breadcrumbs', Breadcrumbs::render('delivery.charge.rule.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('delivery.charge.rules.update', [$rule->id])}}" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name" value="{{ $rule->name }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ $rule->description }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Minimum Basket Size</label>
                        <input type="text" class="form-control" name="min_basket_size" value="{{ $rule->min_basket_size }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Maximum Basket Size</label>
                        <input type="text" class="form-control" name="max_basket_size" value="{{ $rule->max_basket_size }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Delivery Charge</label>
                        <input type="text" class="form-control" name="delivery_charge" value="{{ $rule->delivery_charge }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option @if($rule->status == \App\Enums\DeliveryChargeRuleStatus::ACTIVE) selected @endif value="{{ \App\Enums\DeliveryChargeRuleStatus::ACTIVE }}">Active</option>
                            <option @if($rule->status == \App\Enums\DeliveryChargeRuleStatus::INACTIVE) selected @endif value="{{ \App\Enums\DeliveryChargeRuleStatus::INACTIVE }}">Inactive</option>
                        </select>
                    </div>
                </div>

                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
@endsection
