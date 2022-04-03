<?php

namespace App\Traits;

use App\Models\Participant;
use App\Models\Thread;
use Carbon\Carbon;

trait HasChat
{
    protected $subject, $message, $media = 0;
    protected $recipients = [];
    protected $threadsTable, $messagesTable, $participantsTable;
    protected $threadClass, $participantClass;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->threadsTable = 'threads';
        $this->messagesTable = 'messages';
        $this->participantsTable = 'participants';

        $this->threadClass = Thread::class;
        $this->participantClass = Participant::class;

        parent::__construct($attributes);
    }

    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function writes($message, $media = 0)
    {
        $this->message = $message;
        $this->media = $media;

        return $this;
    }

    public function to($users)
    {
        if (is_array($users)) {
            $this->recipients = array_merge($this->recipients, $users);
        } else {
            $this->recipients[] = $users;
        }

        return $this;
    }


    /**
     * Send new message
     *
     * @return mixed
     */
    public function send()
    {
        $thread = $this->threads()->create([
            'subject' => $this->subject,
        ]);

        // Message
        $message = $thread->messages()->create([
            'user_id' => $this->id,
            'text' => $this->message,
            'media' => $this->media
        ]);

        // Sender
        $participantClass = $this->participantClass;
        $participantClass::create([
            'user_id' => $this->id,
            'thread_id' => $thread->id,
            'seen_at' => Carbon::now()
        ]);

        if (count($this->recipients)) {
            $thread->addParticipants($this->recipients);
        }

//        if ($thread) {
//            event(new NewMessageDispatched($thread, $message));
//        }

        return $thread;
    }

    /**
     * Get user threads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany($this->threadClass);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function reply($thread)
    {
        if (!is_object($thread)) {
            $threadClass = $this->threadClass;
            $thread = $threadClass::whereId($thread)->firstOrFail();
        }

        $thread->activateAllParticipants();

        $message = $thread->messages()->create([
            'user_id' => $this->id,
            'text' => $this->message,
            'media' => $this->media
        ]);

        // Add replier as a participant
        $participantClass = $this->participantClass;
        $participant = $participantClass::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => $this->id
        ]);

        $participant->seen_at = Carbon::now();
        $participant->save();

        $thread->updated_at = Carbon::now();
        $thread->save();

//        event(new NewReplyDispatched($thread, $message));

        return $message;
    }

    /**
     * Get the threads that have been sent by a user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function sent()
    {
        return $this->participated()
            ->where("{$this->threadsTable}.user_id", $this->id)
            ->latest('updated_at');
    }

    /**
     *
     * @param bool $withTrashed
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function participated($withTrashed = false)
    {
        $query = $this->belongsToMany($this->threadClass, $this->participantsTable, 'user_id', 'thread_id')
            ->withPivot('seen_at')
            ->with($this->participantsTable)
            ->withTimestamps();

//        if (!$withTrashed) {
//            $query->whereNull("{$this->participantsTable}.deleted_at");
//        }

        return $query;
    }

    /**
     * Get unread messages
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function unread()
    {
        return $this->received()->whereNull('seen_at');
    }

    /**
     * Get the threads that have been sent to the user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function received()
    {
        // todo: get only the received messages if they got an answer
        return $this->participated()->latest('updated_at');
    }
}
