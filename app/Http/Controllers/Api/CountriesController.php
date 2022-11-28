<?php

namespace App\Http\Controllers\Api;

use App\Mail\PasswordEmail;
use App\Models\City;
use App\Models\Country;
use App\Models\Port;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CountriesController extends Controller
{
    public function getCountries(Request $request)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        $query = Country::query();

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function test44()
    {
        ini_set('max_execution_time', 0);
        $data = array_map('str_getcsv', file('D:\Projects\dealGo\world-cities.csv'));
        foreach ($data as $index => $item) {
            if ($index) {
                $country = Country::where(DB::raw('LOWER(name_en)'), 'like', '%' . rtrim(ltrim(strtolower($item[1]))) . '%')->first();
                if ($country) {
                    $city = City::where(DB::raw('LOWER(name_en)'), 'like', '%' . rtrim(ltrim(strtolower($item[0]))) . '%')->first();
                    if (!$city) {
                        $city = new City;
                        $city->country_id = $country->id;
                        $city->name_ar = rtrim(ltrim($item[0]));
                        $city->name_en = rtrim(ltrim($item[0]));
                        $city->name_fr = rtrim(ltrim($item[0]));
                        $city->code = '01';
                        $city->save();
                    }
                } else {
                    $country = new Country;
                    $country->name_ar = rtrim(ltrim($item[1]));
                    $country->name_en = rtrim(ltrim($item[1]));
                    $country->name_fr = rtrim(ltrim($item[1]));
                    $country->code = '01';
                    $country->save();

                    $city = new City;
                    $city->country_id = $country->id;
                    $city->name_ar = rtrim(ltrim($item[0]));
                    $city->name_en = rtrim(ltrim($item[0]));
                    $city->name_fr = rtrim(ltrim($item[0]));
                    $city->code = '01';
                    $city->save();
                }

            }
        }
    }

    public function test()
    {
        ini_set('max_execution_time', 0);

//        foreach (User::all() as $item) {
//
//            $item->secret = Str::random(40);
//
//
//            $data = [
//                'username' => $item->email,
//                'secret' => $item->secret,
//                'email' => $item->email,
//                'first_name' => $item->contact_name,
//                'last_name' => '',
//                'custom_json' =>'none'
//            ];
//
//            $item->save();
//
//            $response = Http::withHeaders([
//                'PRIVATE-KEY' => env('CHATENGINE_PROJECT_KEY'),
//            ])->post('https://api.chatengine.io/users/', $data);
//
////            dd($response);
//        }
//        dd(1);

        $data = array_map('str_getcsv', file('/home/u310033965/domains/dealgo.net/public_html/admin/port.csv'));
        foreach ($data as $index => $item) {
            if ($index) {

                $country = Country::where(DB::raw('LOWER(name_en)'), 'like', '%' . rtrim(ltrim(strtolower($item[0]))) . '%')->first();
                if ($country) {

                    $city = City::where(DB::raw('LOWER(name_en)'), 'like', '%' . rtrim(ltrim(strtolower($item[0]))) . '%')->first();

                    if ($city) {

                        $port = Port::where(DB::raw('LOWER(name)'), 'like', '%' . rtrim(ltrim(strtolower($item[1]))) . '%')->where('city_id', $city->id)->first();

                        if ($port) {
                            continue;
                        } else {
//                            dd($item);
                            if ($item[2] && $item[12] && $item[13]) {
                                $port = new Port;
                                $port->city_id = $city->id;
                                $port->unlocode = $item[2];
                                $port->latitude = str_replace('°', '', rtrim(ltrim($item[12])));
                                $port->longitude = str_replace('°', '', rtrim(ltrim($item[13])));
                                $port->status = 1;
                                $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                                    ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                                    ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                            } else {
                                $port = new Port;
                                $port->city_id = $city->id;
                                $port->unlocode = null;
                                $port->latitude = 0.0;
                                $port->longitude = 0.0;
                                $port->status = 1;
                                $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                                    ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                                    ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                            }

                        }
                    } else {

                        $city = City::where('country_id', $country->id)->first();

                        if (!$city) {
                            $city = new City;
                            $city->country_id = $country->id;
                            $city->name_ar = rtrim(ltrim($item[0]));
                            $city->name_en = rtrim(ltrim($item[0]));
                            $city->name_fr = rtrim(ltrim($item[0]));
                            $city->code = '01';
                            $city->save();
                        }

                        if ($item[2] && $item[12] && $item[13]) {
                            $port = new Port;
                            $port->city_id = $city->id;
                            $port->unlocode = $item[2];
                            $port->latitude = str_replace('°', '', rtrim(ltrim($item[12])));
                            $port->longitude = str_replace('°', '', rtrim(ltrim($item[13])));
                            $port->status = 1;
                            $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                                ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                                ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                        } else {
                            $port = new Port;
                            $port->city_id = $city->id;
                            $port->unlocode = null;
                            $port->latitude = 0.0;
                            $port->longitude = 0.0;
                            $port->status = 1;
                            $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                                ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                                ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                        }
                    }
                } else {
                    $country = new Country;
                    $country->name_ar = rtrim(ltrim($item[0]));
                    $country->name_en = rtrim(ltrim($item[0]));
                    $country->name_fr = rtrim(ltrim($item[0]));
                    $country->code = '01';
                    $country->save();

                    $city = City::where('country_id', $country->id)->first();

                    if (!$city) {
                        $city = new City;
                        $city->country_id = $country->id;
                        $city->name_ar = rtrim(ltrim($item[0]));
                        $city->name_en = rtrim(ltrim($item[0]));
                        $city->name_fr = rtrim(ltrim($item[0]));
                        $city->code = '01';
                        $city->save();
                    }

                    $city = new City;
                    $city->country_id = $country->id;
                    $city->name_ar = rtrim(ltrim($item[0]));
                    $city->name_en = rtrim(ltrim($item[0]));
                    $city->name_fr = rtrim(ltrim($item[0]));
                    $city->code = '01';
                    $city->save();

                    if ($item[2] && $item[13] && $item[12]) {
                        $port = new Port;
                        $port->city_id = $city->id;
                        $port->unlocode = $item[2];
                        $port->latitude = str_replace('°', '', rtrim(ltrim($item[12])));
                        $port->longitude = str_replace('°', '', rtrim(ltrim($item[13])));
                        $port->status = 1;
                        $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                            ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                            ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                    } else {
                        $port = new Port;
                        $port->city_id = $city->id;
                        $port->unlocode = null;
                        $port->latitude = 0.0;
                        $port->longitude = 0.0;
                        $port->status = 1;
                        $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                            ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                            ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                    }
                }
            }
////                    $newcat->parent_id = $cat->id;
////                    $newcat->setTranslation('name', 'ar', rtrim(ltrim($item[0])))
////                        ->setTranslation('name', 'tr', rtrim(ltrim($item[0])))
////                        ->setTranslation('name', 'en', rtrim(ltrim($item[0])))
////                        ->setTranslation('description', 'ar', rtrim(ltrim($item[2])))
////                        ->setTranslation('description', 'tr', rtrim(ltrim($item[2])))
////                        ->setTranslation('description', 'en', rtrim(ltrim($item[2])))->save();
//
//                }
////                $subcat = null;
////
////                if ($item[1]) {
////                    $subcat = vType::where('name', 'like', '%' . rtrim(ltrim($item[1])) . '%')->first();
////                    if (!$subcat) {
////                        $subcat = new vType;
////                        $subcat->parent_id = $cat->id;
////                        $subcat->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
////                            ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
////                            ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
////                    }
////                }
////                $subsubcat = null;
////                if ($item[2] && $subcat) {
////                    $subsubcat = vType::where('name', 'like', '%' . rtrim(ltrim($item[2])) . '%')->first();
////                    if (!$subsubcat) {
////                        $subsubcat = new vType;
////                        $subsubcat->parent_id = $subcat->id;
////                        $subsubcat->setTranslation('name', 'ar', rtrim(ltrim($item[2])))
////                            ->setTranslation('name', 'tr', rtrim(ltrim($item[2])))
////                            ->setTranslation('name', 'en', rtrim(ltrim($item[2])))->save();
////                    }
////                }
//            }
        }

        return response()->success();
    }

    function DMStoDD($input)
    {
        $deg = " ";
        $min = " ";
        $sec = " ";
        $inputM = " ";


        print "<br> Input is " . $input . " <br>";

        for ($i = 0; $i < strlen($input); $i++) {
            $tempD = $input[$i];
            //print "<br> TempD [$i] is : $tempD";

            if ($tempD == iconv("UTF-8", "ISO-8859-1//TRANSLIT", '°')) {
                $newI = $i + 1;
                //print "<br> newI is : $newI";
                $inputM = substr($input, $newI, -1);
                break;
            }//close if degree

            $deg .= $tempD;
        }//close for degree

        //print "InputM is ".$inputM." <br>";

        for ($j = 0; $j < strlen($inputM); $j++) {
            $tempM = $inputM[$j];
            //print "<br> TempM [$j] is : $tempM";

            if ($tempM == "'") {
                $newI = $j + 1;
                //print "<br> newI is : $newI";
                $sec = substr($inputM, $newI, -1);
                break;
            }//close if minute
            $min .= $tempM;
        }//close for min

//        dd($deg, $min, $sec);
        return $deg + ((($min * 60) + ($sec)) / 3600);

        print "<br> Degree is " . $deg * 1;
        print "<br> Minutes is " . $min;
        print "<br> Seconds is " . $sec;
        print "<br> Result is " . $result;


        return $deg + ($min / 60) + ($sec / 3600);

    }

    public function test2()
    {
        Mail::to('akramlazkanee@hotmail.com')->send(new PasswordEmail(1234));
        return response()->success();
    }
}
