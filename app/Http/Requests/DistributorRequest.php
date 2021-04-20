<?php

namespace App\Http\Requests;


use Illuminate\Routing\Route;

class DistributorRequest extends BaseRequest
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
            $this->setRule('mobile', ['required']);
            $this->setRule('lat', ['nullable']);
            $this->setRule('long', ['nullable']);
            $this->setRule('areas', ['required']);
            $this->setRule('email', ['required','email']);
            $this->setRule('contact_person_name', ['required']);
            $this->setRule('profile_image', ['image', 'max:5120', 'dimensions:ratio=1']);
            $this->setRule('banner_image', ['image', 'max:5120', 'dimensions:ratio=4/3']);
        });

        return $this->getRules();
    }
}
