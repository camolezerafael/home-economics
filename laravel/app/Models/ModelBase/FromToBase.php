<?php

namespace App\Models\ModelBase;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class FromToBase
 * @package App\Models\ModelBase
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property Transaction[] $transaction
 */
class FromToBase extends ModelBase
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'from_to';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    /**
     * @return string[]
     */
    public static function keys(): array
    {
        return [
            'name',
            'type',
        ];
    }


    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

}
