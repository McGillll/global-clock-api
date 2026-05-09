<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CountdownSequenceItem;

class Countdown extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'duration_seconds',
        'notes',
        'is_active',
        'background_color',
        'font_color',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'is_active' => 'boolean',
            'background_color' => 'string',
            'font_color' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sequenceItems(): HasMany
    {
        return $this->hasMany(CountdownSequenceItem::class);
    }
}
