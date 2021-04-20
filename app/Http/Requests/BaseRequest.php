<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    protected $rules = [];
    protected $messages = [];

    protected function setRule($field, array $rule = [])
    {
        if (is_array($field)) {
            $this->rules = array_merge($this->rules, $field);
        }

        if (is_string($field)) {
            $this->rules[$field] = $rule;
        }

        return $this;
    }

    protected function appendRules($field, array $rules)
    {
        if ($rule = $this->getInputRules($field)) {
            array_push($rule, ...$rules);
            $this->rules[$field] = $rule;

            return $this;
        }

        $this->setRule($field, $rules);

        return $this;
    }

    protected function prependRules($field, array $rules)
    {
        if ($rule = $this->getInputRules($field)) {
            array_push($rules, ...$rule);
            $this->rules[$field] = $rule;

            return $this;
        }

        $this->setRule($field, $rules);

        return $this;
    }

    protected function removeRules($field, array $rules)
    {
        if ($existence_rules = $this->getInputRules($field)) {

            foreach ($rules as $rule) {
                foreach ($existence_rules as $key => $value) {
                    if (starts_with($value, $rule)) {
                        unset($existence_rules[$key]);
                    }
                }
            }

            $this->rules[$field] = $existence_rules;
        }

        return $this;
    }

    protected function forUpdate(callable $fn)
    {
        if (request()->method() == 'PUT' || request()->method() == 'PATCH') {
            $fn($this);
        }

        return $this;
    }

    protected function forCreate(callable $fn)
    {
        if (request()->method() == 'POST') {
            $fn($this);
        }

        return $this;
    }

    public function httpMethod($method, callable $fn)
    {
        if (is_string($method) || !is_array($method)) {
            $method = [$method];
        }

        $method = array_map(function ($val) {
           return strtoupper($val);
        }, $method);

        if (in_array(request()->method(), $method)) {
            $fn($this);
        }

        return $this;
    }

    protected function getInputRules($field)
    {
        $rules = null;

        if (isset($this->rules[$field])) {
            $rules = $this->rules[$field];

            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }
        }

        return $rules;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public abstract function rules();
}
