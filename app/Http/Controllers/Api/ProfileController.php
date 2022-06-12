<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helpers;
use App\Models\City;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class ProfileController extends Controller
{
    public function getProfile($id = null)
    {
        if (!$id) {
            $id = auth('api')->user()->id;
        }

        $user = User::with('userable')->withCount('userable.contracts')->withCount('userable.contracts.shipments')->find($id);
        if (!$user) {
            return response()->error('objectNotFound');
        }

        return response()->success($user);
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

        return response()->success($notification);
    }

    public function updateProfile(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'gsm' => 'required|numeric',
                'image' => 'required',
                'type' => 'required',
                'gender' => 'numeric',
                'dob' => 'date',
                'city_id' => 'required_if:type,2,3|numeric',
                'coordinates' => 'required_if:type,3|string',
                'field' => 'required_if:type,3|numeric',
                'preferred_type' => 'required_if:type,2|numeric',
                'commercial_record' => 'string',
                'profession_record' => 'string',
                'password' => 'string',
                'tags' => 'array'
            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters', $validator->failed());
        }

        $user_id = auth('api')->user()->id;

        $user = User::find($user_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->gsm = $request->gsm;
        $user->profile_pic = $request->image;


        if ($user->type === 1) {
            $requestDob = $request->input('dob', null);
            if ($requestDob) {
                $dob = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->dob)));
                $user->dob = $dob;
            }
            $user->gender = $request->input('gender', null);

        } elseif ($user->type === 2) {

            $city = City::find($request->city_id);

            if (!$city) {
                return response()->error('objectNotFound');
            }

            $user->gender = $request->input('gender', null);
            $requestDob = $request->input('dob', null);
            if ($requestDob) {
                $dob = $request->dob ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->dob))) : null;
                $user->dob = $dob;
                $user->provider()->update([
                    'dob' => $dob,
                ]);
            }

            $user->provider()->update([
                'city_id' => $request->city_id,
                'profession_record' => $request->profession_record,
                'preferred_type' => $request->preferred_type
            ]);
        } elseif ($user->type === 3) {

            $city = City::find($request->city_id);

            if (!$city) {
                return response()->error('objectNotFound');
            }

            $user->company()->update([
                'field' => $request->field,
                'coordinates' => $request->coordinates,
                'city_id' => $request->city_id,
                'commercial_record' => $request->commercial_record
            ]);
        }

        if (sizeof($request->input('tags', [])) > 0) {
            foreach ($request->input('tags') as $tag) {
                if (Tag::find($tag)) {
                    $user->tags()->attach($tag);
                }
            }
        }

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

        $user = auth('api')->user();

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
