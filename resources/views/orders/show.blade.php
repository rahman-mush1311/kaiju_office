@extends('layouts.viewer')

@section('title', 'Customer Order')

@section('heading', 'Deligram.com - Order #'.$order->tracking_id)

@section('contents')
<div class="card">
    <div class="card-header" style="padding: 15px 10px;">
        <div class="col-sm-6">
            @foreach($statuses as $value)
            @if($order->status == $value)
            <div class="alert alert-{{ $order->status == 1 ? 'info' : ($order->status == 2 ? 'warning' : ($order->status == 3 ? 'success' : 'danger')) }}" style="display: inline-block;padding:15px;"><strong>Order {{ trans('order.status.'.$value) }}</strong></div>
            @endif
            @endforeach
        </div>
        <div class="col-sm-6">Created: <strong>{{ $order->created_at }}</strong>
            <div>Last Updated: <strong>{{ $order->updated_at }}</strong></div>
        </div>
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-sm-6">
                <div style="padding: 15px;">
                    <h6 class="mb-2">Customer:</h6>
                    <div>
                        <strong>{{ $order->customer->name }}</strong><br/>
                        Shop Name: {{ $order->customer->shop_name ?? '' }}<br/>
                    </div>
                    <div class="mb-2">{{ $order->address}}</div>
                    <div><a href="tel:{{ $order->customer->mobile }}" class="btn btn-primary">{{ $order->customer->mobile }}</a></div>
                </div>
            </div>

            <div class="col-sm-6">
                <div style="padding: 15px;">
                    <h6 class="mb-2">Distributor:</h6>
                    <div>
                        <strong>{{ $order->distributor->name }}</strong>
                    </div>
                    <div>Area: {{ join(',', array_filter($order->distributor->area->pluck('name')->toArray())) }}</div>
                    <div class="mb-2">Address: {{ $order->distributor->address }}</div>
                    <div><a href="tel:{{ $order->distributor->mobile }}" class="btn btn-primary">{{ $order->distributor->mobile }}</a></div>
                </div>
            </div>



        </div>


        <hr/>


        @php
        $line_items = optional($order)->line_items ?? [];

        $select2Config = [
        'name' => 'product_id',
        'data_source' => route('distributor.products'),
        'attribute' => 'name_en',
        'allow_clear' => false,
        'query_params' => [
        'entity' => 'product',
        'distributor_id' => $order->distributor_id,
        'status'=> \App\Enums\ProductStatus::ACTIVE,
        ],
        'placeholder' => 'Select a product',
        'minimum_input_length' => 0,
        'exclude_selected' => true
        ];

        $status = optional($order)->status ?? \App\Enums\OrderStatus::CREATED;

        $new_item_template = view('orders.single-line-item', get_defined_vars())->render();
        @endphp
        <label><strong>Order Line Items</strong></label>
        <table class="line-items table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">QTY</th>
                    <th scope="col">Item Total</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($line_items))
                @foreach($line_items as $index=>$item)
                @include('orders.single-line-item-row', [
                'status' => $status,
                'item' => $item,
                'product' => [
                'id' => $item->product_id,
                'name' => optional($item)->product ? $item->product->name : '',
                ]
                ])
                @endforeach
                @endif
                <tr>
                    <td></td>
                    <td></td>
                    <td>Invoice Subtotal</td>
                    <td>৳{{ number_format($order->sub_total, 2, '.', '') ?? 0 }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Delivery Charge</td>
                    <td>৳{{ number_format($order->delivery_charge, 2, '.', '') ?? 0 }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><strong>Order Total</strong></td>
                    <td><strong>৳{{ number_format($order->total, 2, '.', '') ?? 0 }}</strong></td>
                </tr>
            </tbody>

        </table>
        <div class="row">
            <div class="form-group col-md-12 ">
                <label>Order Remarks</label>
                <pre class="alert alert-light">{{ $order->remarks }}</pre>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
@endpush


@push('scripts')
<script>
    $(function() {
        $("input,textarea,select").attr("readonly", "readonly");
        $(".main-content").css("minWidth", ($("table.line-items").width() + 110) + "px");
    });
</script>
@endpush
