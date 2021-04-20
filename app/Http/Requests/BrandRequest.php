<?php

namespace App\Http\Requests;


class BrandRequest extends BaseRequest
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
           $this->setRule('name_en', ['required']);
           $this->setRule('name_bn', ['required']);
           $this->setRule('description', ['required']);
           $this->setRule('image', ['nullable', 'image']);
        });



        return $this->getRules();
    }
}
