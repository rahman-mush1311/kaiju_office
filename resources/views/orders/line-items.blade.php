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
<div class="col-md-12">
    <strong>Order line items</strong>
    <div class="row">
        <div class="col-md-5"><label>Product</label></div>
        <div class="col-md-2"><label>Price</label></div>
        <div class="col-md-1"><label>Qty</label></div>
        <div class="col-md-3"><label>Item Total</label></div>
    </div>
    <div class="line-items-container" data-select2-config='@json($select2Config)'  data-new-item-template="{{ $new_item_template }}">
        @if(!empty($line_items))
            @foreach($line_items as $index=>$item)
                @include('orders.single-line-item', [
                    'status' => $status,
                    'item' => $item,
                    'product' => [
                        'id' => $item->product_id,
                        'name' => optional($item)->product ? $item->product->name : '',
                    ]
                ])
            @endforeach
        @endif
    </div>
    <div class="row">
        <div class="col-11 m-b-10">
            <div class="float-right col-md-2 padding-0">
                <input type="text" class="form-control" id="invoice_subtotal" name="sub_total" readonly value="{{ number_format($order->sub_total, 2, '.', '') ?? 0 }}" />
            </div>
            <div class="float-right col-md-3 m-t-10"><label class="float-right" for="">Invoice Subtotal</label></div>
        </div>

        <div class="col-11 m-b-10">
            <div class="float-right col-md-2 padding-0">
                <input type="text" class="form-control" id="delivery_charge" name="delivery_charge" readonly value="{{ number_format($order->delivery_charge, 2, '.', '') ?? 0 }}" />
            </div>
            <div class="float-right col-md-3 m-t-10"><label class="float-right" for="">Delivery Charge</label></div>
        </div>

        <div class="col-11">
            <div class="float-right col-md-2 padding-0">
                <input type="text" class="form-control" id="invoice_total" name="total" readonly value="{{ number_format($order->total, 2, '.', '') ?? 0 }}" />
            </div>
            <div class="float-right col-md-3 m-t-10"><label class="float-right" for="">Order Total</label></div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .hidden {
            display: none;
        }
        .m-b-10 {
            margin-bottom: 10px;
        }
        .padding-0 {
            padding: 0;
        }
        .m-t-10 {
            margin-top: 10px;
        }
    </style>
@endpush
