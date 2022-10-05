<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helpers;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Validator;

class ProfileController extends Controller
{
    public function getProfile($id = null)
    {
        if (!$id) {
            $id = auth('api')->user()->id;
        }

        $user = User::with(['userable', 'city.country'])->withCount(['notifications' => function ($q) {
            $q->where('seen', 0);
        }])->find($id);
        if (!$user) {
            return response()->error('objectNotFound');
        }

        $user->rate_list = Rate::where('rated_id', $user->id)->with('rater')->get();
        $user->append(['rate', 'is_ratable', 'offers']);

        return response()->success($user->append(['user_contracts_count', 'user_shipments_count', 'user_payments_sum', 'user_next_payment']));
    }

    public function getNotificationsCount()
    {

        $user = User::withCount(['notifications' => function ($q) {
            $q->where('seen', 0);
        }])->find(auth('api')->user()->id);
        if (!$user) {
            return response()->error('objectNotFound');
        }
        return response()->success($user->notifications_count);
    }

    public function getProfileRates($id)
    {
        $rated = User::/*whereHasMorph('userable', [Owner::class])->*/ where('status', 1)->where('id', $id)->first();

        $rates = Rate::where('rated_id', $rated->id)->with('rater')->get();

        return response()->success($rates);
    }

    public function rate($id, Request $request)
    {
        $user = User::/*whereHasMorph('userable', [Tenant::class])->*/ where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $validator = Validator::make($request->all(),
            [
                'rate' => 'required|numeric',
                'message' => 'required|string',
            ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $rated = User::/*whereHasMorph('userable', [Owner::class])->*/ where('status', 1)->where('id', $id)->first();

        if (!$rated) {
            return response()->error('objectNotFound');
        }

        $rate = Rate::where('rated_id', $rated->id)->where('rater_id', $user->id)->first();

        if ($rate) {
            return response()->error('alreadyRated');
        }

        $rate = new Rate;
        $rate->rater_id = $user->id;
        $rate->rated_id = $rated->id;
        $rate->rate = $request->rate;
        $rate->message = $request->message;
        $rate->save();

        return response()->success();
    }

    public function registerFCMToken(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'token' => 'present',
            ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $user_token = $request->input('token');

        if (!$user_token) {
            FCMToken::where('user_id', auth('api')->user()->id)->delete();

        } else {

            $token = new FCMToken;

            $token->text = $user_token;
            $token->language = $request->header('Accept-Language');
            $token->user_id = auth('api')->user()->id;
            $token->save();
        }
        return response()->success();
    }

    public function getNotifications(Request $request)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        return response()->success(Notification::where('user_id', auth('api')->user()->id)->skip(($page_number - 1) * $page_size)->take($page_size)->orderBy('created_at', 'desc')->get());
    }

    public function getNotification($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->error('objectNotFound');
        }

        $notification->seen = 1;

        $notification->save();

        return response()->success($notification);
    }

    public function updateProfile(Request $request)
    {
        $user_id = auth('api')->user()->id;
        $user = User::find($user_id);

        $validator = Validator::make($request->all(),
            [
                'business_name' => Rule::requiredIf($user->type === 1),
                'commercial' => Rule::requiredIf($user->type === 1),
                'company' => Rule::requiredIf($user->type === 1),
                'license' => Rule::requiredIf($user->type === 1),
                'contact_name' => 'required|string',
                'zip' => 'required|string',
                'province' => 'required|string',
                'address_1' => 'required|string',
                'address_2' => 'nullable|string',
                'city' => 'required|numeric',
                'email' => 'required',
                'phone' => 'required',
                'legal' => 'required',
                'image' => 'required',
            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters', $validator->failed());
        }

        if ($user->type === 1) {
            $user->full_name = $request->business_name;
            $user->company_file = $request->company_file;
            $user->license_file = $request->license_file;
            $user->commercial_number = $request->commercial;
        }
        $user->contact_name = $request->contact_name;
        $user->zip_code = $request->zip;
        $user->province = $request->province;
        $user->address_1 = $request->address_1;
        $user->address_2 = $request->address_2;
        $user->city_id = $request->city;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->legal_file = $request->legal_file;
        $user->image = $request->image;
        $user->files = json_encode($request->input('files', []));

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->error('alreadyExist');
        }

        return response()->success();
    }

    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'password' => 'required',

            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters', $validator->failed());
        }

        $user = User::find(auth('api')->user()->id);

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->success();
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'base64' => 'required',
            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters', $validator->failed());
        }

        $image_64 = $request->input('base64');

        if (preg_match('/^data:image\/(\w+);base64,/', $image_64)) {

            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;

            Storage::disk('public_images')->put($imageName, base64_decode($image));

            return response()->success(['name' => $imageName]);

        } else {
            return response()->error('missingParameters', $validator->failed());
        }
    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'file' => 'required|file|mimetypes:image/jpeg,image/png,video/avi,video/mpeg,video/mp4,video/ogg,video/webm,application/pdf,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation|max:10240',
            ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $fileName = null;

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('file'), $fileName);
        }

        return response()->success(['name' => $fileName]);
    }
}
