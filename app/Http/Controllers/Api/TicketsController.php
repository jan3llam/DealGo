<?php

namespace App\Http\Controllers\Api;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class TicketsController extends Controller
{
    public function reply($id, Request $request)
    {
        $user_id = auth('api')->user()->id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = Ticket::where('id', $id)->where('user_id', $user_id)->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }

        $reply = new Reply;

        $reply->ticket_id = $item->id;
        $reply->author_id = $user_id;
        $reply->author_type = User::class;
        $reply->text = $request->text;

        $item->replies()->save($reply);

        $item->status = 2;

        $item->save();

        return response()->success();
    }

    public function add(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'subject' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $ticket = new Ticket;

        $ticket->subject = $request->subject;
        $ticket->user_id = $user_id;
        $ticket->description = $request->description;
        $ticket->type = $request->type;
        $ticket->status = 1;

        $ticket->save();

        return response()->success();
    }

    public function list(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $data = [];
        $search_clm = ['user.name', 'user.email', 'user.phone', 'user.contact_name',
            'admin.name', 'admin.email', 'admin.phone', 'subject', 'description'];


        $params = $request->all();
        $query = Ticket::where('user_id', $user_id)->with([
            'user',
            'admin' => function ($q) {
                $q->withTrashed();
            }
        ]);

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

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

        if ($filter_status !== null) {
            switch ($filter_status) {
                case 1:
                {
                    $query->where('status', 1);
                    break;
                }
                case 2:
                {
                    $query->where('status', 2);
                    break;
                }
                case 3:
                {
                    $query->where('status', 3);
                    break;
                }
                case 4:
                {
                    $query->onlyTrashed();
                    break;
                }
            }
        }

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->get();

        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function get($id, Request $request)
    {
        $user_id = auth('api')->user()->id;

        $query = Ticket::with([
            'user',
            'replies',
            'admin' => function ($q) {
                $q->withTrashed();
            }
        ]);
        $query->where('id', $id)->where('user_id', $user_id);

        $data = $query->first();

        if (!$data) {
            return response()->error('objectNotFound');
        }

        return response()->success($data);
    }

    public function delete($id)
    {
        $user_id = auth('api')->user()->id;

        $item = Ticket::where('id', $id)->where('user_id', $user_id)->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }

        if ($item) {
            $item->status = 3;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }

}
