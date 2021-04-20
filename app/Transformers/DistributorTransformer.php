<?php


namespace App\Transformers;


use Nahid\Presento\Transformer;

class DistributorTransformer extends Transformer
{
    public function getNameProperty( $value )
    {
        return trans_table_column($value);
    }

    public function getBrandsProperty( $value )
    {
        $brands = [];
        $data = collect($value)->pluck('brands')->flatten(1);
        foreach ($data as $brand) {
            $brands[$brand['id']] = [
                "id" =>  $brand['id'],
                "name" => trans_table_column($brand['name']),
                "tagline" => $brand['description'],
                "slug" => $brand['slug'],
                "logo_url" => $brand["image"],
                "status" => $brand["status"],
                "active_products" => $brand['active_products'],
            ];
        }

        return array_values($brands);
    }
}
