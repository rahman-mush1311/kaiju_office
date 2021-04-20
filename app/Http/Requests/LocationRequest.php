<?php

namespace App\Http\Requests;


class LocationRequest extends BaseRequest
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
            $this->setRule('name.en', ['required']);
            $this->setRule('name.bn', ['required']);
        });

        return $this->getRules();
    }
}
