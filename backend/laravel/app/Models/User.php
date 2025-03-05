<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'active',
        'hourly_rate',
        'total_hours',
        'total_earnings',
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

    public function isPremium(): bool 
    {
        return $this->plan === "premium";
    }

    public function company()
{
    return $this->belongsTo(Company::class);
}

public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
        ->withPivot('created_at') 
        ->withTimestamps();
}

public function isAdmin()
{
    return $this->roles()->where('name', 'Admin')->exists();
}

public function appointments()
{
    return $this->hasMany(Appointment::class);
}

public function reports()
{
    return $this->hasMany(Appointment::class)->whereNotNull('report'); // Nur Termine mit Bericht
}

public function invoices()
{
    return $this->hasMany(Appointment::class)->where('billed', 1); // Nur fakturierte Termine
}
}
