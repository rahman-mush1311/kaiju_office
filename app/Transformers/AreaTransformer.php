<?php


namespace App\Transformers;


use Nahid\Presento\Transformer;

class AreaTransformer extends Transformer
{
    public function getNameProperty( $value )
    {
        return trans_table_column($value);
    }
}
