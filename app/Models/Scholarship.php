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
        'type',
        'settings',
        'is_active',
        'application_deadline',
    ];

    protected $casts = [
        'settings' => 'array',  // This tells Laravel to automatically convert between JSON and PHP array
        'is_active' => 'boolean',
        'application_deadline' => 'date',
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

    // Helper method to check if scholarship accepts applications
    public function isAcceptingApplications(): bool
    {
        return $this->is_active &&
            $this->application_deadline &&
            $this->application_deadline->isFuture();
    }

    // Helper to get required documents
    public function getRequiredDocuments(): array
    {
        return $this->settings['required_documents'] ?? [];
    }

    // Helper to get scholarship requirements
    public function getRequirements(): array
    {
        return $this->settings['requirements'] ?? [];
    }
}