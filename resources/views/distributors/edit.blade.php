@php
$selectedLocations = $distributor->area->pluck('location_id')->toArray();
$selectedAreas = $distributor->area->pluck('id')->toArray();
@endphp

@extends('layouts.master')

@section('title', 'Edit Distributor')

@section('heading', 'Edit Distributor')

@section('heading_buttons')
    <a href="{{route('distributors.index')}}" class="btn btn-primary">All Distributors</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('distributors.edit'))

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}">
@endpush


@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('distributors.update', [$distributor->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ $distributor->user_id }}">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name (EN)</label>
                        <input type="text" class="form-control" name="name[en]" value="{{ $distributor->name_en ?? '' }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Name (BN)</label>
                        <input type="text" class="form-control" name="name[bn]" value="{{ $distributor->name_bn ?? '' }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Contact Person Name</label>
                        <input type="text" class="form-control" name="contact_person_name" value="{{ $distributor->contact_person_name ?? '' }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Mobile</label>
                        <input type="text" class="form-control" name="mobile" value="{{ $distributor->mobile ?? '' }}">
                    </div>
                </div>


                <div class="form-row">

                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $distributor->email ?? '' }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            @foreach(trans('distributor.status') as $value => $item)
                                <option value="{{ $value }}" @if($distributor->status == $value) selected @endif>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="location">Locations</label>
                        <select id="location" name="locations[]" class="form-control select2multiple">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @if(in_array($location->id, $selectedLocations)) @endif >{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="area">Areas</label>
                        <select id="area" name="areas[]" class="form-control select2multiple">
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" @if(in_array($area->id, $selectedAreas)) @endif >{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="form-group">
                    <label for="lat">Address</label>
                    <input type="text" id="address" class="form-control" name="address" value="{{ $distributor->address ?? '' }}" >
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="lat">Latitude</label>
                        <input type="text" id="lat" class="form-control" name="lat" value="{{ $distributor->lat ?? '' }}" >
                    </div>

                    <div class="form-group col-md-6">
                        <label for="long">Longitude</label>
                        <input type="text" id="long" class="form-control" name="long" value="{{ $distributor->long ?? '' }}" >
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="min-order-value">Minimum Order Value</label>
                        <input type="text" id="min-order-value" class="form-control" name="minimum_order_value" value="{{ $distributor->minimum_order_value ?? '' }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="delivery-charge-rule">Delivery Charge Rules</label>
                        <select id="delivery-charge-rule" name="delivery_charge_rules[]" class="form-control select2multiple">
                            @foreach($deliveryChargeRules as $rule)
                                <option value="{{ $rule->id }}">{{ $rule->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control">
                    <div>Format: jpg,png | Ratio:1/1 | Max File Size: 5 Mb</div>
                </div>

                <div class="form-group">
                    <label for="">Banner Image</label>
                    <input type="file" name="banner_image" class="form-control">
                    <div>Format: jpg,png | Ratio:4/3 | Max File Size: 5 Mb</div>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                @method('PUT')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            if(jQuery().timepicker && $(".timepicker").length) {
                $(".timepicker").timepicker({
                    icons: {
                        up: 'fas fa-chevron-up',
                        down: 'fas fa-chevron-down'
                    }
                });
            }
        });
    </script>
@endpush
@push('stack_js')
    <script>
        $(document).ready(function () {
            $('.select2multiple').each(function(){
                $(this).select2({
                    multiple: true,
                });
            });

            @if(!empty($selectedLocations))
                $('#location').val(@json($selectedLocations)).trigger('change');
            @endif

            @if(!empty($selectedAreas))
                $('#area').val(@json($selectedAreas)).trigger('change');
            @endif

            @if(!empty($assignedRules))
                $('#delivery-charge-rule').val(@json($assignedRules)).trigger('change');
            @endif


            $('#area').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('area.search') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            locations: $('#location').val()
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    }
                }
            });
        });
    </script>
@endpush
