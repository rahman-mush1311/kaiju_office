<?php


namespace App\Transformers;


use Nahid\Presento\Transformer;

class AgentTransformer extends Transformer
{
    public function getContactNameProperty( $value )
    {
        return trans_table_column($value);
    }
}
