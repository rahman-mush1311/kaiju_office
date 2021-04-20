@extends('layouts.master')

@section('title', 'Edit Order')

@section('heading', 'Edit Order #'.$order->tracking_id)


@section('breadcrumbs', Breadcrumbs::render('order.edit'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <div>
                <strong>Created:</strong> {{ $order->created_at }}</br></br>
                <strong>Last Updated:</strong> {{ $order->updated_at }}</br></br>
            </div>

            <form action="{{route('order.update', [$order->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                    <div class="form-group col-md-4">
                        <label>Shop Name</label>
                        <input type="text" class="form-control" value="{{ $order->customer->shop_name }}" disabled>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Customer</label>
                        <input type="text" class="form-control" value="{{ $order->customer->name }}" disabled>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Customer Mobile</label>
                        <input type="text" class="form-control" value="{{ $order->customer->mobile }}" disabled>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach($statuses as $value)
                                <option value="{{ $value }}" @if($order->status == $value) selected @endif>{{ trans('order.status.'.$value) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Delivery Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $order->address }}">
                    </div>
                </div>

                <div class="form-row">
                    <input type="hidden" name="distributor_id" value="{{ $order->distributor_id }}">
                    <div class="form-group col-md-6">
                        <label>Distributor</label>
                        <input type="text" class="form-control" value="{{ $order->distributor->name }}" disabled>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Distributor Mobile</label>
                        <input type="text" class="form-control" value="{{ $order->distributor->mobile }}" disabled>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Sales Representative</label>
                        <select name="sales_representative_id" id="sales_representative_id" class="form-control">
                            <option value=""></option>
                            @foreach($salesRepresentatives as $sr)
                                <option value="{{ $sr->id }}" @if($order->sales_representative_id == $sr->id) selected @endif>{{ data_get($sr, 'user.name') }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(!blank($order->remarks))
                        <div class="form-group col-md-6">
                            <label>Customer Notes</label>
                            <textarea id="user-note" class="form-control" style="min-height: 70px; width: 100%;">{{ $order->remarks }}</textarea>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Order Remarks</label>
                        <textarea id="order-remark" name="remarks" class="form-control" style="min-height: 70px; width: 100%;"></textarea>
                    </div>
                </div>

                @include('orders.line-items')

                @method('put')
                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Order Remarks</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-md">
                    <tbody><tr>
                        <th>#</th>
                        <th>Remarks</th>
                        <th>Created By</th>
                        <th>Old Status</th>
                        <th>New Status</th>
                        <th>Created At</th>
                    </tr>

                    @forelse($order->status_history as $history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ data_get($history, 'remarks') }}</td>
                            <td>{{ $history->creator->name }}</td>
                            <td>{{ trans('order.status.'.$history->previous_status) }}</td>
                            <td>{{ trans('order.status.'.$history->current_status) }}</td>
                            <td>{{ $history->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="6" align="center">No Remarks Found</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function calculateItemTotal(parent, discountedPrice, qty) {
            $('.item_total', parent).val((discountedPrice * qty).toFixed(2));
            calculateSubtotal();
        }

        function calculateSubtotal() {
            let subtotal = 0;
            $('.item_total').each(function () {
                if ($(this).val()) {
                    subtotal += parseFloat($(this).val());
                }
            });
            $('#invoice_subtotal').val(subtotal.toFixed(2));
            calculateTotal();
        }

        function getDeliveryCharge(subtotal) {
            let url = '{{ route('order.delivery-charge', ["orderId" => $order]) }}';
            return $.ajax({
                type: "GET",
                data: {
                  "sub_total": subtotal
                },
                url: url,
                async: false
            }).responseText;
        }

        function calculateTotal() {
            let subtotal = parseFloat($('#invoice_subtotal').val());
            let deliveryCharge = parseFloat(getDeliveryCharge(subtotal));
            $('#delivery_charge').val(deliveryCharge.toFixed(2));
            let total = subtotal + deliveryCharge;
            $("#invoice_total").val(total.toFixed(2));
        }

        function addInitLineItem($lineItemsContainer) {
            var newItemTemplate = $lineItemsContainer.data('newItemTemplate');
            var $newItem = $(newItemTemplate);
            $lineItemsContainer.append($newItem);
            initSelect2($newItem.find('.line-item-select'), $lineItemsContainer);
            $('.line-item-delete', $newItem).addClass('hidden');
            $('.line-item-delete').click(function (e) {
                $(this).parent().parent().remove();
                calculateSubtotal();
            });
        }

        function initSelect2(target, $lineItemsContainer) {
            var $target = $(target);
            if (!$target.hasClass("select2-hidden-accessible")){
                var config = $lineItemsContainer.data('select2Config');
                var $select2 = $target.select2({
                    // theme: 'bootstrap',
                    multiple: false,
                    placeholder: 'Search Product',
                    minimumInputLength: 0,
                    allowClear: false,
                    ajax: {
                        url: config.data_source,
                        dataType: 'json',
                        quietMillis: 250,
                        data: function (params) {
                            var data = {
                                search: params.term, // search term
                                page: params.page,
                                widget_type: 'select2'
                            };

                            if (config.query_params) {
                                $.each(config.query_params, function(key, value){
                                    var paramName = key, paramValue = value;
                                    if ($.isPlainObject(value)) {
                                        paramName = value.name || key;
                                        if (value.value) {
                                            paramValue = value.value;
                                        }
                                        else if (value.value_source) {
                                            var $field = $(value.value_source);
                                            if ($field.size() === 1) {
                                                paramValue = $field.val();
                                                if (value.transformation_map && value.transformation_map[paramValue]) {
                                                    paramValue = value.transformation_map[paramValue];
                                                }
                                            }
                                            else if ($field.size() > 1) {
                                                paramValue = {};
                                                $field.each(function() {
                                                    var $this = $(this);
                                                    paramValue[$this.name] = $this.val();
                                                });
                                            }
                                        }
                                    }
                                    data[paramName] = paramValue;
                                });
                            }

                            if (config.exclude_selected) {
                                var selectedIds = [];
                                $lineItemsContainer.find('.product_id').each(function() {
                                    var id = $(this).val();
                                    if (id) {
                                        selectedIds.push(id);
                                    }
                                });

                                if (selectedIds.length) {
                                    data['exclude_ids'] = selectedIds;
                                }
                            }

                            return data;
                        },
                        processResults: function (response) {
                            data = response.map(function (item) {
                                return {
                                    'id' : item.product_id,
                                    'text': `${item.name_en}` + ' - ' + item.short_description,
                                    'quantity': item.min_order_qty,
                                    'price': item.price
                                }
                            });
                            return {
                                results: data
                            };
                        },
                        cache: false
                    }
                });


                if (config.allow_clear) {
                    $select2.on('select2:unselecting', function(e) {
                        $(this).val('').trigger('change');
                        e.preventDefault();
                    });
                }

                $select2.on("select2:select", function(e) {
                    var $data = e.params.data;
                    var $lineItemContainer = $select2.parents('.line-item-container').eq(0);
                    let parent = $(this).parent().parent();
                    $('.product_id', parent).val($data.id);
                    $('.line-item-delete', parent).removeClass('hidden');

                    $lineItemContainer.find('.product_id').val($data.id);
                    $('.item_total', parent).val($data.price.toFixed(2));
                    $('.discounted_price', parent).val($data.price.toFixed(2));
                    $('.quantity', parent).val(1);
                    $('.quantity', parent).attr("max", $data.quantity);
                    $lineItemContainer.find('.line-item-product-name').removeClass('hidden').val($data.text).attr('disabled', 'disabled');
                    $lineItemContainer.find('.select2-container').addClass('hidden');
                    $lineItemContainer.find('.quantity').val(1);
                    $lineItemContainer.find('.line-item-delete').parent().removeClass('hidden');

                    ['.discounted_price', '.quantity', '.item_total'].forEach(function(item) {
                        let elem = $(item, parent);
                        let elemName = elem.attr('name').replace(/__INDEX__/g, $data.id);
                        elem.attr('name', elemName);
                    });

                    addInitLineItem($lineItemsContainer);
                    calculateSubtotal();
                });

                $(document).off('change','.qty').on("change",'.qty', function() {
                    let parent = $(this).parent().parent();
                    let discountedPrice = $('.unit_price', parent).val();
                    calculateItemTotal(parent, discountedPrice, $(this).val());
                });



                if (config.query_params) {
                    $.each(config.query_params, function(key, value){
                        if ($.isPlainObject(value)) {
                            if (value.value_source && value.reset_on_change) {
                                var $field = $(value.value_source);
                                $field.change(function(e){
                                    e.preventDefault();
                                    $lineItemsContainer.empty();
                                    addNewItem($lineItemsContainer);
                                });
                            }
                        }
                    });
                }
            }
        }

        $(document).ready(function () {
            $(document).off('change','.quantity').on("change",'.quantity', function() {
                let parent = $(this).parent().parent();
                let discountedPrice = $('.discounted_price', parent).val();
                calculateItemTotal(parent, discountedPrice, $(this).val());
            });

            $(document).off('click','.line-item-delete').on("click",'.line-item-delete', function() {
                $(this).parent().parent().remove();
                calculateSubtotal();
            });

            @if($order->status == \App\Enums\OrderStatus::CREATED)
                addInitLineItem($('.line-items-container'));
            @endif

            $('#status').change(function(){
                let previousStatus = '{{ $order->status }}';
                let currentStatus = $('#status').val();
                if (previousStatus == currentStatus) {
                    $('#order-remark').attr('required', false);
                } else {
                    $('#order-remark').attr('required', true);
                }
            });
        });
    </script>
@endpush
