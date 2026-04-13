<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'area',
        'contract_type',
        'modality',
        'location',
        'salary_range',
        'salary_negotiable',
        'positions',
        'deadline',
        'status',
        'requires_cv',
        'programs_targeted',
    ];

    protected $casts = [
        'deadline'          => 'date',
        'salary_negotiable' => 'boolean',
        'requires_cv'       => 'boolean',
    ];

    // ─── Relaciones ───────────────────────────────────────────────
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->deadline->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->deadline->isPast();
    }

    public function getRemainingDaysAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }

    public function getSalaryLabelAttribute(): string
    {
        if ($this->salary_negotiable) return 'A convenir';
        return $this->salary_range ?? 'No especificado';
    }

    // Etiquetas de colores según modalidad
    public function getModalityBadgeAttribute(): string
    {
        return match($this->modality) {
            'remote'  => 'bg-info',
            'hybrid'  => 'bg-warning',
            default   => 'bg-primary',
        };
    }

    // Scope para vacantes activas
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('deadline', '>=', now());
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }
}
