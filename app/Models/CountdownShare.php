<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CountdownSequence;

class CountdownShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'countdown_sequence_id',
        'token',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(CountdownSequence::class, 'countdown_sequence_id');
    }
}
