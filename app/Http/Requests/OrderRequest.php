<?php

namespace App\Http\Requests;

use App\Models\Distributor;
use Illuminate\Validation\ValidationException;

class OrderRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

//    public function validationData()
//    {
//        $distributor = Distributor::findOrFail($this->get('distributor_id'));
//
//        if ($distributor->minimum_order_value > $this->get('sub_total')) {
//            throw ValidationException::withMessages(['sub_total' => __("Order subtotal is less than minimum order value :val !", ['val' => $distributor->minimum_order_value])]);
//        }
//
//        return parent::validationData();
//    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->httpMethod(['put', 'patch'], function ($request) {
            $this->setRule('distributor_id', ['required']);
            $this->setRule('sub_total', ['required']);
            $this->setRule('delivery_charge', ['required']);
            $this->setRule('total', ['required']);
            $this->setRule('items.*.product_id', ['nullable']);
            $this->setRule('items.*.discounted_price', [ 'required']);
            $this->setRule('items.*.item_total', ['required']);
        });

        return $this->getRules();
    }
}
