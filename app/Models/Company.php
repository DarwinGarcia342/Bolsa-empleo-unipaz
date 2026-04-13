<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'nit',
        'sector',
        'contact_person',
        'phone',
        'address',
        'description',
        'logo_path',
        'website',
        'status',
    ];

    // ─── Relaciones ───────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }

    public function activeJobPostings()
    {
        return $this->hasMany(JobPosting::class)->where('status', 'active');
    }

    // ─── Helpers ─────────────────────────────────────────────────
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->company_name) . '&background=28a745&color=fff&size=128';
    }
}
