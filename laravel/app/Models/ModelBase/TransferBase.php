<?php

namespace App\Models\ModelBase;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TransferBase
 * @package App\Models\ModelBase
 *
 * @property integer $id
 * @property integer $from_account_id
 * @property integer $to_account_id
 * @property integer $value
 * @property Account[] $account
 */
class TransferBase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transfers';

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
        'from_account_id',
        'to_account_id',
        'value',
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
            'from_account_id',
            'to_account_id',
            'value',
        ];
    }


    public function account()
    {
        return $this->hasMany(Account::class);
    }

}
