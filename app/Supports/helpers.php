<?php

use Illuminate\Log\Logger;

if (!function_exists('auth_user')) {
    function auth_user($guard = 'web')
    {
        if (!auth($guard)->check()) {
            return null;
        }

        return auth($guard)->user();
    }
}

if (!function_exists('item_sl')) {
    function item_sl($index, $page = 1, $perPage = 20)
    {
        $page -= 1;
        return ($page * $perPage) + $index + 1;
    }
}

if (!function_exists('is_valid_mobile_number')) {
    function is_valid_mobile_number($input)
    {
        return preg_match('/^01[3-9]\d{8}$/', strval($input));
    }
}

if(!function_exists('debug_log'))
{
    function debug_log($msg, $context = [], $level = 'info')
    {
        /**
         * @var $logger Logger
         */
        $logger = app(Logger::class);

        if (config('logging.enable')) {
            if (!is_array($context)) {
                $context = [];
            }

            $logger->write($level, $msg, $context);
        }
    }
}

if (!function_exists('api')) {
    /**
     * @param $data
     * @return \App\Supports\ApiJsonResponse
     */
    function api($data = []) {
        $api = new \App\Supports\ApiJsonResponse($data);

        return $api;
    }
}

if (!function_exists('to_array')) {
    function to_array($data) {
        if ($data instanceof Collection) {
            return $data->toArray();
        }

        if ($data instanceof Model || $data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return $data->toArray();
        }

        if (is_object($data)) {
            return (array) $data;
        }

        return $data;
    }
}

if (!function_exists('trans_table_column')) {
    function trans_table_column($column) {
        if (!is_array($column) && !is_object($column)) return $column;

        $locale = strtolower(app()->getLocale() ?? 'en');
        return data_get($column, $locale, null) ?? data_get($column, 'en');
    }
}


if (!function_exists('get_distributor_id')) {
    function get_distributor_id() {
        if (!auth('distributor')->guest()) {
            return auth('distributor')->user()->id;
        } elseif (!auth('sr')->guest()) {
            return auth('sr')->user()->distributor_id;
        }
    }
}

if (!function_exists('get_sr_id')) {
    function get_sr_id() {
        if (!auth('sr')->guest()) {
            return auth('sr')->user()->id;
        }
    }
}
