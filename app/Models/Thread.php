<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['user_id'];
    protected $with = ['user'];


    /**
     * Recipients of this message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipients()
    {
        return $this->participants()->where('user_id', '!=', $this->user_id);
    }

    /**
     * Participants relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants',
            'thread_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * See if the current thread is unread by the user.
     *
     * @param integer $userId
     *
     * @return bool
     */
    public function isUnread($userId = null)
    {
        $userId = $userId ?? auth()->id();

        $participant = $this->participants()
            ->where('user_id', $userId)
            ->first();

        if ($participant && $participant->pivot->seen_at === null) {
            return true;
        }

        return false;
    }

    /**
     * Adds users to this thread
     *
     * @param array $participants list of all participants
     *
     * @return void
     */
    public function addParticipants(array $participants)
    {
        if (count($participants)) {
            $participantClass = Participant::class;
            foreach ($participants as $user_id) {
                if (User::find($user_id)) {
                    $participant = $participantClass::firstOrCreate([
                        'thread_id' => $this->id,
                        'user_id' => $user_id,
                    ]);

                    $participant->seen_at = null;
                    $participant->save();
                }
            }
        }
    }

    /**
     * Restores all participants within a thread that has a new message
     */
    public function activateAllParticipants()
    {
        $participants = $this->participants()->get();

        foreach ($participants as $participant) {
            $participant = $participant->pivot;
            if ($participant) {
//                $participant->deleted_at = null;
                $participant->seen_at = null;
                $participant->save();
            }
        }
    }


    /**
     * Get last message associated with thread.
     *
     * @return object
     */
    public function lastMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Messages relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Scope a query to only include thread form custom users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param                                       $users
     *
     * @return void
     */
    public function scopeFrom($query, $users)
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        $query->whereIn($this->table . '.user_id', $users);
    }

    /**
     * Scope a query to only include thread sent to custom users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param                                       $users
     *
     * @return void
     */
    public function scopeTo($query, $users)
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        $participantsTable = 'participants';

        $query->select($this->table . '.*')
            ->join($participantsTable, "{$this->table}.id", '=', "{$participantsTable}.thread_id")
            ->whereIn("{$participantsTable}.user_id", $users);
    }

    /**
     * Scope a query to only include seen thread.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    public function scopeSeen($query)
    {
        $query->whereNotNull('participants' . '.seen_at');
    }
}
