<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CountdownSequence;
use App\Models\Countdown;

class CountdownSequenceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'countdown_sequence_id',
        'countdown_id',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'countdown_sequence_id' => 'integer',
            'countdown_id' => 'integer',
            'position' => 'integer',
        ];
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(CountdownSequence::class, 'countdown_sequence_id');
    }

    public function countdown(): BelongsTo
    {
        return $this->belongsTo(Countdown::class);
    }
}
