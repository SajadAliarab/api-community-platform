<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Models\Contracts\HasName;

class User extends Authenticatable implements FilamentUser , HasName , HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userName',
        'fName',
        'lName',
        'email',
        'password',
        'type'
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
        'password' => 'hashed',
    ];

    public function user_detail():HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

      public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function getFilamentName(): string
    {
        return "{$this->userName}";
    }
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->user_detail && $this->user_detail->image) {

            return asset('/storage/'. $this->user_detail->image);
        }
        return asset('/storage/avatars/profile.jpg');
    }
    public function isAdmin()
    {
        return $this->type === 'admin';
    }
    public function isExpert()
    {
        return $this->type === 'expert';
    }
    protected static function booted()
    {
        static::created(function ($user) {
            $user->user_detail()->create();
        });
    }

}
