<?php

namespace App\Http\Requests;


class ProductRequest extends BaseRequest
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
        $this->httpMethod('post', function ($request) {
           $this->setRule('name_en', ['required']);
           $this->setRule('name_bn', ['required']);
           $this->setRule('mrp', ['required', 'numeric']);
           $this->setRule('trade_price', ['nullable']);
           $this->setRule('status', ['required']);
           $this->setRule('image', ['image']);
        });

        $this->httpMethod(['put', 'patch'], function ($request) {
           $this->setRule('name_en', ['required']);
           $this->setRule('name_bn', ['required']);
           $this->setRule('mrp', ['required', 'numeric']);
           $this->setRule('trade_price', ['nullable']);
           $this->setRule('status', ['required']);
           $this->setRule('image', ['image']);
        });

        return $this->getRules();
    }
}
