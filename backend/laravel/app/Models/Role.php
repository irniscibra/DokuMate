<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
        ->withTimestamps();
}
}

