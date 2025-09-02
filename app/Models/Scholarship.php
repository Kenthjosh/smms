<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'form_schema',
        'table_config',
        'infolist_config',
        'theme_config',
        'is_active',
    ];

    protected $casts = [
        'form_schema' => 'array',
        'table_config' => 'array',
        'infolist_config' => 'array',
        'theme_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'committee');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }
}