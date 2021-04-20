<?php

namespace App\Http\Requests;


use App\Models\DeliveryChargeRule;
use Illuminate\Validation\ValidationException;

class DeliveryChargeRuleRequest extends BaseRequest
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


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->httpMethod(['post', 'put', 'patch'], function ($request) {
            $this->setRule('name', ['required']);
            $this->setRule('description', ['required']);
            $this->setRule('min_basket_size', ['required', 'numeric', 'min:1', 'max:999999.99']);
            $this->setRule('max_basket_size', ['required', 'numeric', 'min:1', 'max:999999.99']);
            $this->setRule('delivery_charge', ['required', 'numeric', 'min:1', 'max:999999.99']);
        });

        return $this->getRules();
    }
}
