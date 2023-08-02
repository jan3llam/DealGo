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

        $data = VoyageCalculation::findOrFail($id)->where(['id'=>$id,'deleted_at'=>null,'user_id'=>auth('api')->user()->id])->get();

        return response()->success($data);
    }

    public function getAll(Request $request){

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);


        $voyages = VoyageCalculation::where('deleted_at',null)->whereHas('user')->where('user_id',auth('api')->user()->id);

        $total = $voyages->count();

        $data['data']= $voyages->skip(($page_number - 1) * $page_size)
        ->take($page_size)->orderBy('created_at', 'desc')->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;



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
            return response()->error('missingParameters', $validator->errors());
        }
        DB::beginTransaction();
        try {

            $voyage = VoyageCalculation::create([
                'name' => $request->name,
                'details' => $request->details,
                'user_id' => $user->id
            ]);

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            if($e->getCode() == 23000){
                return response()->json(array("code" => $e->getCode(), "message" => "name already taken", "data" => null), 200);
            }
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }

    public function update(Request $request,$id){
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
            $voyage = VoyageCalculation::findOrFail($id);

            $voyage->update([
                'name' => $request->name,
                'details' => $request->details,
                'user_id' => $user->id
            ]);

            $voyage->save();

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }


    public function delete(int $id){

        $voyage = VoyageCalculation::where('id',$id)->first();
        $voyage->deleted_at = Carbon::now();
        $voyage->save();

        return response()->success();
    }
}

