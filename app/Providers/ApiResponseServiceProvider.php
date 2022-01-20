<?php

namespace App\Providers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {

        $factory->macro('success', function ($data = null) use ($factory) {
            $format = [
                'code' => __('api.codes.success.code'),
                'message' => __('api.codes.success.message'),
                'data' => $data,
            ];

            return $factory->make($format);
        });

        $factory->macro('error', function ($code, $data = null) use ($factory) {
            $format = [
                'code' => __('api.codes.' . $code . '.code'),
                'message' => __('api.codes.' . $code . '.message'),
                'data' => $data,
            ];

            return $factory->make($format);
        });

        $factory->macro('httpError', function ($code) use ($factory) {
            $format = [
                'code' => __('api.codes.' . $code . '.code'),
                'message' => __('api.codes.' . $code . '.message'),
                'data' => null,
            ];

            return $factory->make($format, __('api.codes.' . $code . '.code') * -1);
        });

        $factory->macro('datatables', function ($data) use ($factory) {
            return $factory->make($data);
        });


    }
}
