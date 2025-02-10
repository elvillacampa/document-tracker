<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'ip_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEncoder()
    {
        return $this->role === 'encoder';
    }

    public function isViewer()
    {
        return $this->role === 'viewer';
    }

    public function sessions()
    {
        // Here, 'user_id' is the foreign key on the sessions table,
        // and 'id' is the local key on the users table.
        return $this->hasMany(\App\Models\Session::class, 'user_id', 'id');
    }

    public function lastSession()
    {
        return $this->hasOne(\App\Models\Session::class, 'user_id', 'id')
                    ->latest('last_activity');
    }

}
