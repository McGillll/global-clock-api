<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CountdownSequenceItem;
use App\Models\CountdownShare;

class CountdownSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'is_running',
        'current_item_index',
        'started_at',
        'paused_at',
    ];

    protected function casts(): array
    {
        return [
            'is_running' => 'boolean',
            'current_item_index' => 'integer',
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CountdownSequenceItem::class)->orderBy('position');
    }

    public function share(): HasOne
    {
        return $this->hasOne(CountdownShare::class);
    }
}
