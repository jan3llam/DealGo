<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Validator;

class TicketsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Tickets"]
        ];

        return view('content.tickets-list', ['breadcrumbs' => $breadcrumbs]);
    }

    public function view($id)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['link' => "admin/tickets", 'name' => "Tickets"], ['name' => "Ticket details"]
        ];

        $ticket = Ticket::withTrashed()->where('id', $id)->first();

        return view('content.ticket-view', ['breadcrumbs' => $breadcrumbs, 'ticket' => $ticket]);
    }

    public function reply($id, Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'reply' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', __('api.codes.missingParameters.message'))
                ->withInput();
        }

        $item = Ticket::withTrashed()->where('id', $id)->first();

        if ($item->status === 1 || $item->admin->id === auth('admins')->user()->id) {
            $reply = new Reply;

            $reply->ticket_id = $item->id;
            $reply->author_id = auth('admins')->user()->id;
            $reply->author_type = Admin::class;
            $reply->text = $request->reply;

            $item->replies()->save($reply);

            $item->status = 2;
            $item->admin_id = auth('admins')->user()->id;

            $item->save();

            return redirect()->back()
                ->with('success', __('api.codes.success.message'))
                ->withInput();
        } else {
            return redirect()->back()
                ->with('error', __('api.codes.notAuthorized.message'))
                ->withInput();
        }

    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['user.name', 'user.email', 'user.phone', 'user.contact_name',
            'admin.name', 'admin.email', 'admin.phone', 'subject', 'description'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Ticket::query();

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_trashed = isset($params['trashed']) ? $params['trashed'] : 0;
        $per_page = isset($params['length']) ? $params['length'] : 10;

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 3) {
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

        if ($filter_trashed) {
            $query->onlyTrashed();
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip(($page) * $per_page)
            ->with([
                'user' => function ($q) {
                    $q->withTrashed();
                },
                'admin' => function ($q) {
                    $q->withTrashed();
                }
            ])->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function delete($id)
    {
        $item = Ticket::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 3;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id, Request $request)
    {
        $item = Ticket::withTrashed()->where('id', $id)->first();
        if ($item) {

            if ($item->status === 0 && $item->deleted_at !== null) {
                $item->restore();
            }
            $item->status = $request->status;
            $item->save();
        }

        return response()->success();
    }
}
