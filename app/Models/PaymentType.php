<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    use HasFactory;

    protected $table = 'payment_types';

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOrderedForUser($query, $userId)
    {
        return $query->forUser($userId)->orderBy('name');
    }

    public static function firstForUser(int $userId): ?self
    {
        return static::forUser($userId)->orderBy('name')->first();
    }

    public static function keys(): array
    {
        return ['id', 'name', 'description'];
    }
}
