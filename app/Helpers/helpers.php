<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Config;
use GuzzleHttp\Client;

class Helper
{
    public static function applClasses()
    {
        // default data array
        $DefaultData = [
            'mainLayoutType' => 'vertical',
            'theme' => 'light',
            'sidebarCollapsed' => false,
            'navbarColor' => 'bg-info',
            'horizontalMenuType' => 'floating',
            'verticalMenuNavbarType' => 'floating',
            'footerType' => 'static', //footer
            'layoutWidth' => 'boxed',
            'showMenu' => true,
            'bodyClass' => '',
            'pageClass' => '',
            'pageHeader' => true,
            'contentLayout' => 'default',
            'blankPage' => false,
            'defaultLanguage' => 'en',
            'direction' => app()->getLocale() === 'ar' ? 'rtl' : 'ltr',
        ];

        // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
        $data = array_merge($DefaultData, config('custom.custom'));

        // All options available in the template
        $allOptions = [
            'mainLayoutType' => array('vertical', 'horizontal'),
            'theme' => array('light' => 'light', 'dark' => 'dark-layout', 'bordered' => 'bordered-layout', 'semi-dark' => 'semi-dark-layout'),
            'sidebarCollapsed' => array(true, false),
            'showMenu' => array(true, false),
            'layoutWidth' => array('full', 'boxed'),
            'navbarColor' => array('bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'),
            'horizontalMenuType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'),
            'horizontalMenuClass' => array('static' => '', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'),
            'verticalMenuNavbarType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'),
            'navbarClass' => array('floating' => 'floating-nav', 'static' => 'navbar-static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'),
            'footerType' => array('static' => 'footer-static', 'sticky' => 'footer-fixed', 'hidden' => 'footer-hidden'),
            'pageHeader' => array(true, false),
            'contentLayout' => array('default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'),
            'blankPage' => array(false, true),
            'sidebarPositionClass' => array('content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'),
            'contentsidebarClass' => array('content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'),
            'defaultLanguage' => array('en' => 'en', 'ar' => 'ar'),
            'direction' => array('ltr', 'rtl'),
        ];

        //if mainLayoutType value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) === gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if (!array_key_exists($data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }

        //layout classes
        $layoutClasses = [
            'theme' => $data['theme'],
            'layoutTheme' => $allOptions['theme'][$data['theme']],
            'sidebarCollapsed' => $data['sidebarCollapsed'],
            'showMenu' => $data['showMenu'],
            'layoutWidth' => $data['layoutWidth'],
            'verticalMenuNavbarType' => $allOptions['verticalMenuNavbarType'][$data['verticalMenuNavbarType']],
            'navbarClass' => $allOptions['navbarClass'][$data['verticalMenuNavbarType']],
            'navbarColor' => $data['navbarColor'],
            'horizontalMenuType' => $allOptions['horizontalMenuType'][$data['horizontalMenuType']],
            'horizontalMenuClass' => $allOptions['horizontalMenuClass'][$data['horizontalMenuType']],
            'footerType' => $allOptions['footerType'][$data['footerType']],
            'sidebarClass' => '',
            'bodyClass' => $data['bodyClass'],
            'pageClass' => $data['pageClass'],
            'pageHeader' => $data['pageHeader'],
            'blankPage' => $data['blankPage'],
            'blankPageClass' => '',
            'contentLayout' => $data['contentLayout'],
            'sidebarPositionClass' => $allOptions['sidebarPositionClass'][$data['contentLayout']],
            'contentsidebarClass' => $allOptions['contentsidebarClass'][$data['contentLayout']],
            'mainLayoutType' => $data['mainLayoutType'],
            'defaultLanguage' => $allOptions['defaultLanguage'][$data['defaultLanguage']],
            'direction' => $data['direction'],
        ];
        // set default language if session hasn't locale value the set default language
        if (!session()->has('locale')) {
            app()->setLocale($layoutClasses['defaultLanguage']);
        }

        // sidebar Collapsed
        if ($layoutClasses['sidebarCollapsed'] == 'true') {
            $layoutClasses['sidebarClass'] = "menu-collapsed";
        }

        // blank page class
        if ($layoutClasses['blankPage'] == 'true') {
            $layoutClasses['blankPageClass'] = "blank-page";
        }

        return $layoutClasses;
    }

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }

    public static function sendSMS($message, $to)
    {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        try {
            UnifonicFacade::send($to, $message, env('UNIFONIC_SENDER_ID'));
        } catch (\Exception $e) {

        }
    }

    public static function sendNotification($key, $translate = [], $ids = null, $customData = null)
    {
        $title_ar = $title_en = $text_ar = $text_en = '';
        $clickData = [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default",
            "status" => "done",
            "screen" => "screenA",
        ];

        if (is_array($key)) {
            $title_ar = $key['title_ar'];
            $title_en = $key['title_en'];
            $text_ar = $key['text_ar'];
            $text_en = $key['text_en'];
        } else {
            $title_ar = __('notifications.title.' . $key, $translate, 'ar');
            $title_en = __('notifications.title.' . $key, $translate, 'en');
            $text_ar = __('notifications.body.' . $key, $translate, 'ar');
            $text_en = __('notifications.body.' . $key, $translate, 'en');
        }

        $customData = $customData ? $customData : [];

        if (!$ids) {
            $firebaseTokenAr = User::whereHas('FCMTokens', function ($query) {
                return $query->where('language', 'ar');
            })->with('FCMTokens')->get()->pluck('FCMTokens')->collapse()->pluck('text')->toArray();
            $firebaseTokenEn = User::whereHas('FCMTokens', function ($query) {
                return $query->where('language', 'en');
            })->with('FCMTokens')->get()->pluck('FCMTokens')->collapse()->pluck('text')->toArray();
        } else {
            $ids = is_array($ids) ? $ids : [$ids];
            $firebaseTokenAr = User::/*whereHas('FCMTokens', function ($query) {
                return $query->where('language', 'ar');
            })->*/ whereIn('id', $ids)->with('FCMTokens')->get()->pluck('FCMTokens')->collapse()->pluck('text')->toArray();
            $firebaseTokenEn = User::/*whereHas('FCMTokens', function ($query) {
                return $query->where('language', 'en');
            })->*/ whereIn('id', $ids)->with('FCMTokens')->get()->pluck('FCMTokens')->collapse()->pluck('text')->toArray();
            foreach ($ids as $id) {

                $notification = new Notification;
                $notification->user_id = $id;
                $notification->title_ar = $title_ar;
                $notification->title_en = $title_en;
                $notification->text_ar = $text_ar;
                $notification->text_en = $text_en;
                $notification->type = 2;
                $notification->payload = json_encode($customData);
                $notification->save();
            }
        }

        if (!$firebaseTokenAr && !$firebaseTokenEn) {
            return true;
        }

        $server_key = env("FCM_TOKEN");
        $dataAr = [
            "registration_ids" => $firebaseTokenAr,
            "notification" => [
                "title" => $title_ar,
                "body" => $text_ar,
            ],
            "data" => array_merge($customData, $clickData)
        ];

        $dataEn = [
            "registration_ids" => $firebaseTokenEn,
            "notification" => [
                "title" => $title_en,
                "body" => $text_en,
            ],
            "data" => array_merge($customData, $clickData)
        ];

        $dataStringAr = json_encode($dataAr);
        $dataStringEn = json_encode($dataEn);

        $headers = [
            'Authorization' => 'key=' . $server_key,
            'Content-Type' => 'application/json',
        ];

        $client = new Client();

        if (!empty($firebaseTokenAr)) {
            $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => $headers,
                'body' => $dataStringAr
            ]);
        }

        if (!empty($firebaseTokenEn)) {
            $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => $headers,
                'body' => $dataStringEn
            ]);
        }

        return $response;
    }

}
