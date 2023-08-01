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

        $def_port_from = "Bleckede";
        $def_port_to = "Goa";
        echo "<pre>";
        require_once "ports.php";


        $params = $request->all();
        $validator = Validator::make($params, [
            'port_from' => 'required',
            'port_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $portFrom = array_key_exists($params['port_from'],$ports) ? $ports[$params['port_from']] : $def_port_from;
        $portTo = array_key_exists($params['port_to'],$ports) ? $ports[$params['port_to']] : $def_port_to;


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.netpas.net/nea/v7/json/get_distance/?pincode=DEMO&access_code=aldrobi.molham%40gmail.com&piracy_code=001&ports='.$portFrom.'&ports='.$portTo.'&canal_pass_code=011&use_local_eca=false',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $response_array=json_decode($response, true);
        curl_close($curl);
         $data['distance'] = $response_array['total_distance'];
        return response()->success($data);
    }

    public function getById(int $id)
    {

        $data = VoyageCalculation::findOrFail($id)->where(['id' => $id, 'deleted_at' => null, 'user_id' => auth('api')->user()->id])->get();

        return response()->success($data);
    }

    public function getAll(Request $request)
    {

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);


        $voyages = VoyageCalculation::where('deleted_at', null)->whereHas('user')->where('user_id', auth('api')->user()->id);

        $total = $voyages->count();

        $data['data'] = $voyages->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy('created_at', 'desc')->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;


        return response()->success($data);
    }

    public function store(Request $request)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:voyage_calculations',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
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
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }

    public function update(Request $request, $id)
    {
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


    public function delete(int $id)
    {

        $voyage = VoyageCalculation::where('id', $id)->first();
        $voyage->deleted_at = Carbon::now();
        $voyage->save();

        return response()->success();
    }
}

