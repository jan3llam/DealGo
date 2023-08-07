<?php

namespace App\Http\Controllers\Api;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function list(){
        $data = Status::all();

        return response()->success($data);
    }
}
