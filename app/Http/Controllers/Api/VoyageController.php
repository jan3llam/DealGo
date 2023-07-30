<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class VoyageController extends Controller
{
    public function get_distance(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'port_from' => 'required',
            'port_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $portFrom = $params['port_from'];
        $portTo = $params['port_to'];

        $data['distance'] = 677;
        return response()->success($data);
    }

    public function getById(int $id){

    }

    public function getAll(){

    }

    public function store(Request $request){

    }
}

