<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'user_id',
        'type_id',
        'name',
        'description',
        'initial_balance',
        'decimal_precision',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'type_id' => 'integer',
        'initial_balance' => 'integer',
        'decimal_precision' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'type_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getInitialBalanceAttribute($value): float
    {
        $precision = $this->decimal_precision ?? 2;

        return $value / pow(10, $precision);
    }

    public function setInitialBalanceAttribute($value): void
    {
        $precision = $this->decimal_precision ?? 2;
        $this->attributes['initial_balance'] = (int) round($value * pow(10, $precision));
    }

    public function getTypeAttribute(): ?AccountType
    {
        return $this->type;
    }

    public static function keys(): array
    {
        return ['id', 'name', 'description', 'initial_balance' => 'decimal:2', 'decimal_precision', 'type'];
    }
}
