<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawReference extends Model
{
    protected $fillable = [
        'title',
        'description',
        'applicable_to',
        'category',
        'law_number',
        'publication_date',
        'relevant_articles',
        'implementation_notes',
        'is_active',
    ];

    protected $casts = [
        'applicable_to' => 'array',
        'relevant_articles' => 'array',
        'publication_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function isApplicableToRole(string $role): bool
    {
        $applicable = $this->applicable_to ?? ['all'];
        return in_array('all', $applicable) || in_array($role, $applicable);
    }

    public static function getByRole(string $role)
    {
        return static::where('is_active', true)
            ->get()
            ->filter(fn($law) => $law->isApplicableToRole($role));
    }
}
