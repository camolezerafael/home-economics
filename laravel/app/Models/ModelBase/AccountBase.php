<?php

namespace App\Models\ModelBase;

use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountBase
 * @package App\Models\ModelBase
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $description
 * @property integer $initial_balance
 * @property integer $decimal_precision
 * @property integer $type_id
 * @property AccountType $account_type
 * @property User $user
 */
class AccountBase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

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
        'description',
        'initial_balance',
        'decimal_precision',
        'type_id',
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
            'user_id',
            'name',
            'description',
            'initial_balance',
            'decimal_precision',
            'type_id',
        ];
    }


    public function account_type()
    {
        return $this->belongsTo(AccountType::class, 'type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
