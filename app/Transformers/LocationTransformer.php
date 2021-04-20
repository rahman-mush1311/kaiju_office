<?php


namespace App\Transformers;


use Nahid\Presento\Transformer;

class LocationTransformer extends Transformer
{
    public function getNameProperty( $value )
    {
        return trans_table_column($value);
    }
}
