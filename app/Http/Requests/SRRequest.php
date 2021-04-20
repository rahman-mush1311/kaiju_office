<?php

namespace App\Http\Requests;


use Illuminate\Routing\Route;

class SRRequest extends BaseRequest
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
            $this->setRule('distributor_id', ['required']);
            $this->setRule('mobile', ['required']);

            $sr = $request->route('sr');
            if(blank($sr)) {
                $this->setRule('password', ['required']);
            }
        });

        return $this->getRules();
    }
}
