<?php

namespace App\Models\ModelBase;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PaymentTypeBase
 * @package App\Models\ModelBase
 *
 * @property integer $id
 * @property string $name
 * @property Transaction[] $transaction
 */
class PaymentTypeBase extends ModelBase
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_types';

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
		'user_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
		'user_id',
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
        ];
    }


    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

	public function user()
	{
		return $this->belongsTo(User::class);
	}

}
