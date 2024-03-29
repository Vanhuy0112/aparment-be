<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    const ADMIN = 1;
    const CLIENT = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [

        'name',
        'email',
        'phone_number',
        'password',
        'name',
        'dob',
        'number_card',
        'status',
        'apartment_id',
        'role_id',
        'device_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routeNotificationFor($channel): string
    {
        if ($channel === 'PusherPushNotifications') {
            return 'your.custom.interest.string';
        }

        $class = str_replace('\\', '.', get_class($this));

        return $class . '.' . $this->getKey();
    }

    /**
     * @return HasOne
     */
    public function apartment(): HasOne
    {
        return $this->hasOne(Apartment::class);
    }

    /**
     * @return HasOneThrough
     */
    public function bill(): HasOneThrough
    {
        return $this->hasOneThrough(Bill::class, Apartment::class);
    }
}
