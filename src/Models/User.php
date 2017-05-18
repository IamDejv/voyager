<?php

namespace TCG\Voyager\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TCG\Voyager\Contracts\User as UserContract;
use Illuminate\Notifications\Notifiable;
use TCG\Voyager\Notifications\ResetPassword;
use TCG\Voyager\Traits\VoyagerUser;

class User extends Authenticatable implements UserContract
{
    use Notifiable, VoyagerUser;

    protected $guarded = [];

    public function getAvatarAttribute($value)
    {
        if (is_null($value)) {
            return config('voyager.user.default_avatar', 'users/default.png');
        }

        return $value;
    }

    /**
     * On save make sure to set the default avatar if image is not set.
     */
    public function save(array $options = [])
    {
        // If no avatar has been set, set it to the default
        $this->avatar = $this->avatar ?: config('voyager.user.default_avatar', 'users/default.png');

        parent::save();
    }

    /**
     * Send password reset notification
     *
     * @param string $token Password reset token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
