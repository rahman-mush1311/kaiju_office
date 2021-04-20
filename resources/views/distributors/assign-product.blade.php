@extends('layouts.master')

@section('title', 'Distributor Product')

@section('heading', 'Distributor Product')

@section('breadcrumbs', Breadcrumbs::render('distributors.assign-product'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <form id="distributorProductForm"
                              action="{{route('distributors.assign-product', [$distributorId])}}" method="post"
                              enctype="multipart/form-data">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Brands</label>
                                    <select class="form-control select2" multiple id="brands">
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Select Product</label>
                                    <select name="product_id" class="form-control select2"
                                            id="product-search" required>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Trade Price</label>
                                    <input type="text" class="form-control" name="distributor_price"
                                           value="" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Minimum Order Qty</label>
                                    <input type="text" class="form-control" name="min_order_qty"
                                           value="" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Stock Status</label>
                                    <select name="status" class="form-control select2" required id="product-status" required>
                                        <option value=""></option>
                                        @foreach(trans('distributor.product_status') as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @csrf
                            <button type="submit" class="btn btn-success">Save</button>
                        </form>

                    </div>
                </div>
            </div>
            <div style="margin: 20px 10px"><h4>My Products</h4></div>
            <div class="table-wrapper">
                <table class="table-responsive card-list-table">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Trade Price</th>
                        <th scope="col">Min Order Qty</th>
                        <th scope="col">Stock Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($distributorProducts as $distributorProduct)
                        <tr>
                            <td data-title="Name">{{ $distributorProduct->product->name }} - {{ $distributorProduct->product->short_description }}</td>
                            <td data-title="Trade Price">BDT {{ $distributorProduct->distributor_price }}</td>
                            <td data-title="Min Qty">{{ $distributorProduct->min_order_qty }}</td>
                            <td data-title="Status">{{ trans('distributor.product_status.'.$distributorProduct->status) }}</td>
                            <td data-title="Actions">
                                <a href="#" onclick="editProduct(
                                    '{{ $distributorProduct->product_id }}',
                                    '{{ $distributorProduct->product->name }} - {{ $distributorProduct->product->short_description }}',
                                    '{{ $distributorProduct->distributor_price }}',
                                    '{{ $distributorProduct->min_order_qty }}',
                                    '{{ $distributorProduct->status }}',
                                    )" class="btn btn-sm btn-info">Edit</a>
                                <a href="{{route('distributors.detach-product', [$distributorId, $distributorProduct->product->id])}}" class="btn btn-sm btn-danger">Delete</a>                                
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('stack_js')
    <script>
        let selectedProducts = [];
        @if (!blank($distributorProducts))
            selectedProducts = '@json($distributorProducts->pluck('product_id')->toArray())'
            @endif
            console.log(selectedProducts);
            function editProduct(productId, name, distributorPrice, minOrderQty, productStatus) {
                $('#product-search').empty();
                $('#product-search').append(new Option(name, productId, false, false)).trigger('change');
                $('#distributorProductForm input[name="distributor_price"]').val(distributorPrice);
                $('#distributorProductForm input[name="min_order_qty"]').val(minOrderQty);
                $('#product-status').val(productStatus).trigger('change');
            }
        $(document).ready(function () {
            $('#product-search').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('product.search') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            brands: $('#brands').val(),
                            selected_products: selectedProducts,
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    }
                }
            });

            $('#product-search').on('select2:select', function (e) {
                let tradePrice = e.params.data.trade_price;
                if (tradePrice) {
                    $('#distributorProductForm input[name="distributor_price"]').val(tradePrice);
                }
            });
        });
    </script>
@endpush
