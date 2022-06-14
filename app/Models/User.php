<?php

namespace App\Models;

use App\Traits\HasChat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable, SoftDeletes, HasChat;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function FCMTokens()
    {
        return $this->hasMany(FCMToken::class, 'user_id', 'id');
    }

    public function codes()
    {
        return $this->hasMany(Code::class, 'user_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

    public function userable()
    {
        return $this->morphTo();
    }

    public function getUserContractsCountAttribute()
    {
        return $this->userable->contracts->count();
    }

    public function getUserPaymentsSumAttribute()
    {
        return $this->userable->contracts->reduce(function ($sum, $item) {
            return $sum + ($item->payments->sum('value'));
        }, 0);

//        return $this->with(['userable.contracts.payments' => function ($query) {
//            $query->select('value');
//        }])->sum('value');

    }

    public function getUserNextPaymentAttribute()
    {
        return $this->with('userable.contacts.payments')->orderBy('userable.contacts.payments.date')->where('paid', 0)->limit(1);
    }

    public function getUserShipmentsCountAttribute()
    {
        return $this->userable->contracts->reduce(function ($count, $item) {
            return $count + $item->shipments->count();
        }, 0);
    }


}
