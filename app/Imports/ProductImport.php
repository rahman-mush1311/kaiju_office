<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (blank($row['mrp'])) {
            return [];
        }

        $data = [
            'name' => [
                'en' => $row['name_en'],
                'bn' => $row['name_bn'],
            ],
            'mrp' => $row['mrp'],
            'trade_price' => $row['list_price'],
            'short_description' => $row['short_description'],
            'long_description' => $row['long_description'],
        ];

        if (empty($row['product_id'])) {
            $product = new Product();
            $product->fill($data);
            $product->save();
        } else {
            $product = Product::find($row['product_id']);
        }

        if (!blank($product)) {
            $product->fill($data);

            $brands = [];
            if (!empty($row['brand_id'])) {
                $brands = explode(',', $row['brand_id']);
            }
            $product->brands()->sync($brands);
            return $product;
        }
    }
}
