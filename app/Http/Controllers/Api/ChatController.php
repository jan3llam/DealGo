<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Helpers\Helpers;
use App\Models\Participant;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;

class ChatController extends Controller
{
    protected $threadClass, $participantClass;

    public function __construct()
    {
        $this->threadClass = Thread::class;
        $this->participantClass = Participant::class;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $user = User::find(auth('api')->user()->id);
//        if ($request->has('sent')) {
//            $threads = $user->sent();
//        } else {
//            $threads = $user->received();
//        }
        $threads = $user->participated();

        $threads = $threads->orderBy('created_at', 'desc')->get();

        return response()->success($threads);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required',
                'text' => 'required',
                'media' => 'required|numeric',
            ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        if (!User::find($request->user_id)) {
            return response()->error('objectNotFound');
        }

        $thread = Participant::whereIn('thread_id', Participant::where('user_id', auth('api')->user()->id)->get()->pluck('thread_id'))
            ->where('user_id', '!=', auth('api')->user()->id)->where('user_id', $request->user_id)->first();

        if ($thread) {
            return response()->error('relationAlreadyExist', ["thread_id" => $thread->thread_id]);
        }

        $thread = User::find(auth('api')->user()->id)
            ->writes($request->text, $request->input('media', 0))
            ->to($request->user_id)
            ->send();

        broadcast(new MessageSent(auth('api')->user(), $request->text))->toOthers();

//        Helpers::sendNotification('new_chat', ['username' => auth('api')->user()->name], $request->user_id);

        return response()->success(["thread_id" => $thread->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function get($thread)
    {
        $user = User::find(auth('api')->user()->id);
        $threadClass = $this->threadClass;
        $thread = $threadClass::find($thread);

        if (!$thread) {
            return response()->error('objectNotFound');
        }

        $messages = $thread->messages()->get();

        $seen = $thread->participants()
            ->where('user_id', $user->id)
            ->first();

        if ($seen && $seen->pivot) {
            $seen->pivot->seen_at = Carbon::now();
            $seen->pivot->save();
        }

        return response()->success(['thread' => $thread, 'messages' => $messages]);
    }
//

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function reply($thread, Request $request)
    {
        $user = User::find(auth('api')->user()->id);

        $threadClass = $this->threadClass;

        $thread = $threadClass::find($thread);

        $validator = Validator::make($request->all(),
            [
                'text' => 'required',
                'media' => 'required|numeric',
            ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        if (!$thread) {
            return response()->error('objectNotFound');
        }

        $message = $user
            ->writes($request->text, $request->input('media', 0))
            ->reply($thread);

        broadcast(new MessageSent($user, $request->text))->toOthers();

        return response()->success();
    }

    public function check($id, Request $request)
    {
        $thread = Participant::whereIn('thread_id', Participant::where('user_id', auth('api')->user()->id)->get()->pluck('thread_id'))
            ->where('user_id', '!=', auth('api')->user()->id)->where('user_id', $id)->first();

        if ($thread) {
            return response()->success(["thread_id" => $thread->thread_id]);
        }
        return response()->success(["thread_id" => null]);
    }

    public function unread(Request $request)
    {
        $count = User::where('id', auth('api')->user()->id)->first()->unread()->count();

        if ($count) {
            return response()->success(["unread_count" => $count]);
        }
        return response()->success(["unread_count" => null]);
    }

    public function getAll(Request $request)
    {

        $response = Http::withHeaders([
            'PRIVATE-KEY' => env('CHATENGINE_PROJECT_KEY'),
        ])->get('https://api.chatengine.io/users/');

        return response()->success(json_decode($response->body()));
    }


//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param Thread $thread
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy($thread)
//    {
//        $threadClass = $this->threadClass;
//        $thread = $threadClass::findOrFail($thread);
//
//        $message = Participant::where('user_id', auth()->id())
//            ->where('thread_id', $thread->id)
//            ->firstOrFail();
//
//        $deleted = $message->delete();
//
//        return redirect()
//            ->route(config('inbox.route.name') . 'inbox.index')
//            ->with('message', [
//                'type' => $deleted ? 'success' : 'error',
//                'text' => $deleted ? trans('inbox::messages.thread.deleted') : trans('inbox::messages.thread.whoops'),
//            ]);
//    }
}
