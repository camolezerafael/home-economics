<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FromTo extends Model
{
    use HasFactory;

    protected $table = 'from_to';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public const TYPE_FRM = 'FRM';  // From & To (Pessoa + Estabelecimento)

    public const TYPE_TO = 'TO';    // To (Estabelecimento)

    public const TYPE_FT = 'FT';    // From (Pessoa)

    public const TYPES = [self::TYPE_FRM, self::TYPE_TO, self::TYPE_FT];

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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function keys(): array
    {
        return ['id', 'name', 'description', 'type'];
    }
}
