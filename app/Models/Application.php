<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_posting_id',
        'cover_letter',
        'cv_path',
        'status',
        'company_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    // ─── Labels de estado ─────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'Pendiente',
            'reviewed'  => 'En revisión',
            'interview' => 'Entrevista programada',
            'accepted'  => 'Aceptado',
            'rejected'  => 'No seleccionado',
            default     => 'Pendiente',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'bg-secondary',
            'reviewed'  => 'bg-info',
            'interview' => 'bg-warning',
            'accepted'  => 'bg-success',
            'rejected'  => 'bg-danger',
            default     => 'bg-secondary',
        };
    }
}
