@php
    $item = $item ?? null;
    $productId = !blank($item) ? array_get($item, 'product_id') : "__INDEX__";
    $product = optional($item)->product ?? null;
@endphp

<tr>
    <td>
        {{ !blank($product) ? array_get($product, 'name') .' - '. array_get($product, 'short_description') : '' }}
    </td>
    <td>
        ৳{{ !blank($item) ? number_format($item->discounted_price, 2, '.', '') : '' }}
    </td>
    <td>
        {{ !blank($item) ? $item->qty : '' }}
    </td>
    <td>
        ৳{{ !blank($item) ? number_format($item->item_total, 2, '.', '') : ''  }}
    </td>
</tr>
