<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Models\User;
use App\Models\VoyageCalculation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $data = VoyageCalculation::findOrFail($id);

        return response()->success($data);
    }

    public function getAll(Request $request){

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $order_field = 'created_at';
        $order_sort = 'desc';

        $data = VoyageCalculation::all()->skip(($page_number - 1) * $page_size)
        ->take($page_size)->orderBy($order_field, $order_sort)->get();

        return response()->success($data);
    }

    public function store(Request $request){
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        DB::beginTransaction();
        try {

            $voyage = VoyageCalculation::create([
                'name' => $request->name,
                'details' => $request->details
            ]);

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }

    public function update(Request $request){
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        DB::beginTransaction();
        try {
            $voyage = VoyageCalculation::findOrFail($request->id);
            $voyage->update([
                'name' => $request->name,
                'details' => $request->details
            ]);

            $voyage->save();

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }
}

