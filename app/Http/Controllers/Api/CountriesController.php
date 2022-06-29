<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\Country;
use App\Models\Port;
use DB;
use Illuminate\Http\Request;

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

    public function test()
    {
        $data = array_map('str_getcsv', file('/home/u990379777/domains/dealgo.site/public_html/Ports.csv'));
        foreach ($data as $index => $item) {
            if ($index) {

                $country = Country::where(DB::raw('LOWER(name_en)'), 'like', '%' . rtrim(ltrim(strtolower($item[0]))) . '%')->first();
                if ($country) {

                    $city = City::where(DB::raw('LOWER(name_en)'), 'like', '%' . rtrim(ltrim(strtolower($item[2]))) . '%')->where('country_id', $country->id)->first();

                    if ($city) {
                        $port = new Port;
                        $port->city_id = $city->id;
                        $port->unlocode = $item[3];
                        $port->latitude = $item[13];
                        $port->longitude = $item[14];
                        $port->status = 1;
                        $port->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
                            ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
                            ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
                    }
//                    $newcat->parent_id = $cat->id;
//                    $newcat->setTranslation('name', 'ar', rtrim(ltrim($item[0])))
//                        ->setTranslation('name', 'tr', rtrim(ltrim($item[0])))
//                        ->setTranslation('name', 'en', rtrim(ltrim($item[0])))
//                        ->setTranslation('description', 'ar', rtrim(ltrim($item[2])))
//                        ->setTranslation('description', 'tr', rtrim(ltrim($item[2])))
//                        ->setTranslation('description', 'en', rtrim(ltrim($item[2])))->save();

                }
//                $subcat = null;
//
//                if ($item[1]) {
//                    $subcat = vType::where('name', 'like', '%' . rtrim(ltrim($item[1])) . '%')->first();
//                    if (!$subcat) {
//                        $subcat = new vType;
//                        $subcat->parent_id = $cat->id;
//                        $subcat->setTranslation('name', 'ar', rtrim(ltrim($item[1])))
//                            ->setTranslation('name', 'tr', rtrim(ltrim($item[1])))
//                            ->setTranslation('name', 'en', rtrim(ltrim($item[1])))->save();
//                    }
//                }
//                $subsubcat = null;
//                if ($item[2] && $subcat) {
//                    $subsubcat = vType::where('name', 'like', '%' . rtrim(ltrim($item[2])) . '%')->first();
//                    if (!$subsubcat) {
//                        $subsubcat = new vType;
//                        $subsubcat->parent_id = $subcat->id;
//                        $subsubcat->setTranslation('name', 'ar', rtrim(ltrim($item[2])))
//                            ->setTranslation('name', 'tr', rtrim(ltrim($item[2])))
//                            ->setTranslation('name', 'en', rtrim(ltrim($item[2])))->save();
//                    }
//                }
            }
        }
    }
}
