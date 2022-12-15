<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Owner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class OffersController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:55', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:53', ['only' => ['add']]);
        $this->middleware('permission:54', ['only' => ['edit', 'status']]);
        $this->middleware('permission:56', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Offers')]
        ];

        $owners = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->get();

        return view('content.offers-list', [
            'breadcrumbs' => $breadcrumbs,
            'owners' => $owners,
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['vessel.name', 'vessel.owner.user.contact_name', 'vessel.owner.user.full_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Offer::query();

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
        $per_page = isset($params['length']) ? $params['length'] : 10;

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 4) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->whereHas($item[2], function ($quer) use ($item, $search_val) {
                                    $quer->where($item[3], 'like', '%' . $search_val . '%');
                                });
                            });
                        })->get();
                    } elseif (sizeof($item) == 3) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->where($item[2], 'like', '%' . $search_val . '%');
                            });
                        })->get();
                    } elseif (sizeof($item) == 2) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->where($item[1], 'like', '%' . $search_val . '%');
                        })->get();
                    } elseif (sizeof($item) == 1) {
                        $q->orWhere($item[0], 'like', '%' . $search_val . '%');
                    }
                }
            });
        }

        if ($sort_field) {
            $order_field = $sort_field;
            $order_sort = $params['direction'];
        }

        if ($filter_status !== null) {
            switch ($filter_status) {
                case 1:
                {
                    $query->withoutTrashed();
                    break;
                }
                case 2:
                {
                    $query->onlyTrashed();
                    break;
                }
            }
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)
            ->with([
                'vessel' => function ($q) {
                    $q->withTrashed()->with(['owner' => function ($qu) {
                        $qu->withTrashed()->with('user', function ($que) {
                            $que->withTrashed();
                        });
                    }]);
                }, 'port_from'])->withCount('responses')->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }


    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'description' => 'required|string',
            'port_from' => 'required|numeric',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'weight' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Offer;

        $item->vessel_id = $params['vessel'];
        $item->port_from = $params['port_from'];
        $item->date_from = Carbon::parse($params['date_from'])->toDateString();
        $item->date_to = Carbon::parse($params['date_to'])->toDateString();
        $item->description = $params['description'];
        $item->weight = $params['weight'];

        $files = $request->file('files', []);
        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $item->files = json_encode($filesArr);

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'description' => 'required|string',
            'port_from' => 'required|numeric',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'weight' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Offer::withTrashed()->where('id', $id)->first();

        $item->vessel_id = $params['vessel'];
        $item->port_from = $params['port_from'];
        $item->date_from = Carbon::parse($params['date_from'])->toDateString();
        $item->date_to = Carbon::parse($params['date_to'])->toDateString();
        $item->description = $params['description'];
        $item->weight = $params['weight'];

        $files = $request->file('files', []);
        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $item->files = json_encode($filesArr);

        $item->save();

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Offer::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = Offer::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }

    public function status($id)
    {
        $item = Offer::withTrashed()->where('id', $id)->first();
        if ($item) {

            if ($item->status === 0 && $item->deleted_at !== null) {
                $item->restore();
            }
            $item->status = $item->status == 1 ? 0 : 1;
            $item->save();
        }

        return response()->success();
    }
}
