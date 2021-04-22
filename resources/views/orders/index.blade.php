@extends('layouts.master')

@section('title', 'Order List')

@section('heading', 'Order List')

@section('breadcrumbs', Breadcrumbs::render('order.index'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/css/daterangepicker.css') }}">
@endpush

@section('contents')
    <form action="{{route('order.index')}}" enctype="multipart/form-data" method="GET">
        @can('isAdmin')
        <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Select Status</option>
                                @foreach(trans('order.status') as $key => $status)
                                    <option value="{{ $key }}"
                                            @if(array_get($input, 'status') == $key) selected @endif>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

{{--                        <div class="form-group col-md-3">--}}
{{--                            <label for="payment_status">Status</label>--}}
{{--                            <select id="payment_status" name="payment_status" class="form-control">--}}
{{--                                <option value="">Select Payment Status</option>--}}
{{--                                @foreach(trans('order.payment_status') as $key => $status)--}}
{{--                                    <option value="{{ $key }}"--}}
{{--                                            @if(array_get($input, 'payment_status') == $key) selected @endif>{{ $status }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}

                        <div class="form-group col-md-2">
                            <label for="customer_id">Retailer</label>
                            <select id="customer_id" name="customer_id" class="form-control select2" data-placeholder="Select Retailer">
                                @if(!blank($customer))
                                    <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="distributor_id">Distributor</label>
                            <select id="distributor_id" name="distributor_id" class="form-control select2" data-placeholder="Select Distributor">
                                @if(!blank($distributor))
                                    <option value="{{ $distributor->id }}">{{ str_replace('"', '', $distributor->name_en) }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Order Date</label>
                            <input type="text" name="order_date" class="form-control" id="order-date" value="{{ request()->get('order_date', '') }}">
                        </div>

                        <div class="col-md-2" style="margin-top: 30px">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            @if(!empty(array_filter($input)))
                                <a href="{{ route('order.index') }}" class="btn btn-light">Remove Filters</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        <div class="card">
            <div class="card-header">
                @can('isAdmin')
                <h4>All Orders</h4>
                @endcan
                <div class="card-header-form">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search"
                               value="{{ $input['search'] ?? '' }}">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table-responsive card-list-table">
                    <thead>
                    <tr>
                        <th scope="col">Order ID</th>
                       <!-- <th scope="col">Real ID</th>
                        <th scope="col">Dist ID</th>-->
                        <th scope="col">Shop Name</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Customer Mobile</th>
                        <th scope="col">Status</th>
                        <th scope="col">Order Total</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Distributor Name</th>
                        <th scope="col">Distributor Mobile</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $index = $orders->firstItem(); @endphp
                    @foreach($orders as $order)
                        <tr>
                            <td data-title="Order ID">{{ $order->tracking_id ?? '' }}</td>
                           <!-- <td data-title="Real ID">{{ $order->id}}</td>
                            <td data-title="Distributor ID">{{ $order->distributor_id}}</td>-->
                            <td data-title="Shop Name">{{ $order->customer->shop_name ?? '' }}</td>
                            <td data-title="Customer">{{ $order->customer->name ?? '' }}</td>
                            <td data-title="Mobile">{{ $order->customer->mobile ?? '' }}</td>
                            <td data-title="Status">{{ trans('order.status.'.$order->status ?? '') }}</td>
                            <td data-title="Total">৳{{ number_format($order->total,2) ?? '' }}</td>
                            <td data-title="Date">{{ $order->created_at }}</td>
                            <td data-title="Distributor">{{ $order->distributor->name ?? '' }}</td>
                            <td data-title="Dist. Contact">{{ $order->distributor->mobile ?? '' }}</td>
                            <td data-title="Actions">
                               <!--  <a href="{{ route('order.show', [$order->tracking_id ?? '']) }}" class="btn btn-sm btn-info">View</a> -->
                                <a href="{{ route('order.edit', [$order->id]) }}" class="btn btn-sm btn-info">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="dataTables_info" id="table-1_info" role="status" aria-live="polite">
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="float-right">
                            {{ $orders->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('stack_js')
    <script src="{{ asset('/assets/js/daterangepicker.js') }}"></script>
    <script>
        $(document).ready(function(){
            if($("#order-date").length) {
                $('#order-date').daterangepicker({
                    autoUpdateInput: false,
                    autoApply: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'DD/MM/YYYY'
                    },
                });

                $("#order-date").on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                });

                $("#order-date").on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
            }


            $('#customer_id').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('customers.search.select2') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    }
                }
            });

            $('#distributor_id').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('distributors.search.select2') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
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
