<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'scholarship_id',    // Added for scholarship relationship
        'role',             // Added for role-based access
        'profile_data',     // Added for flexible profile storage
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_data' => 'array',
    ];

    // Relationship: User belongs to a scholarship
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    // Relationship: User has many applications
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Relationship: User can verify documents
    public function verifiedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'verified_by');
    }

    // Relationship: User can review applications
    public function reviewedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'reviewed_by');
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCommittee(): bool
    {
        return $this->role === 'committee';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // Check if user is super admin (admin with no scholarship)
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && is_null($this->scholarship_id);
    }

    // Get user's full name from profile data if available
    public function getFullNameAttribute(): string
    {
        return $this->profile_data['full_name'] ?? $this->name;
    }

    // Get user's contact number from profile data
    public function getContactNumberAttribute(): ?string
    {
        return $this->profile_data['contact_number'] ?? null;
    }
}
