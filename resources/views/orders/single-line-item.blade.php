@php
    $item = $item ?? null;
    $productId = !blank($item) ? array_get($item, 'product_id') : "__INDEX__";
    $product = optional($item)->product ?? null;
@endphp

<div class="row line-item-container" style="margin-bottom: 10px;">
<div class="col-md-5">
    @if(!blank($item))
        <input type="text" class="form-control" value="{{ !blank($product) ? array_get($product, 'name') .' - '. array_get($product, 'short_description') : '' }}" readonly>
    @else
        <input type="text" class="form-control hidden line-item-product-name" value="">
        <select class="line-item-select form-control"></select>
    @endif
</div>

<div class="col-md-2">
    <input type="text" class="form-control discounted_price" name="items[{{ $productId }}][discounted_price]" value="{{ !blank($item) ? number_format($item->discounted_price, 2, '.', '') : '' }}" readonly />
</div>

<div class="col-md-1">
    <input type="number" style="min-width: 55px; padding:0; text-align:center;" min="1" class="form-control quantity" name="items[{{ $productId }}][qty]" value="{{ !blank($item) ? $item->qty : '' }}" @if($status > \App\Enums\OrderStatus::CREATED) readonly @endif/>
</div>

<div class="col-md-3">
    <input type="text" class="form-control item_total" name="items[{{ $productId }}][item_total]" value="{{ !blank($item) ? number_format($item->item_total, 2, '.', '') : ''  }}" readonly/>
</div>

<div class="col-md-1">
    <button class="btn btn-danger line-item-delete {{ $status > \App\Enums\OrderStatus::CREATED  ? 'hidden' : '' }}" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
</div>

<input type="hidden" class="product_id" name="items[{{ $productId }}][product_id]" value="{{ $productId }}">
    <br>
</div>
