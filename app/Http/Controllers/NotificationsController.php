<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Validator;

class NotificationsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "الرئيسية"], ['name' => "إرسال إشعار"]
        ];

        return view('content.send-notification', ['breadcrumbs' => $breadcrumbs]);
    }

    public function send(Request $request)
    {
        $validation = [];
        $validation['title_ar'] = 'required|string';
        $validation['text_ar'] = 'required|string';
        $params = $request->all();
        $validator = Validator::make($params, $validation);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', __('general.generalError'))
                ->withInput();
        }

        $users = User::where('active', 1)->get();

        foreach ($users as $user) {
            $notification = new Notification;
            $notification->user_id = $user->id;
            $notification->title_ar = $params['title_ar'];
            $notification->title_en = $params['title_ar'];
            $notification->text_ar = $params['text_ar'];
            $notification->text_en = $params['text_ar'];
            $notification->type = 1;
            $notification->save();
        }
//        dd(User::all()->pluck('id'));

        Helper::sendNotification($params, null, User::all()->pluck('id'));

        return redirect()->route('admin.home')
            ->with('success', __('general.changesSaved'));
    }


}
