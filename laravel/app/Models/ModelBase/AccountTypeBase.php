<?php

namespace App\Models\ModelBase;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AccountTypeBase
 * @package App\Models\ModelBase
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property Accounts[] $accounts
 */
class AccountTypeBase extends ModelBase
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_types';

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
            'description',
        ];
    }


    public function accounts()
    {
        return $this->hasMany(Accounts::class);
    }

	public function user()
	{
		return $this->belongsTo(User::class);
	}

}
