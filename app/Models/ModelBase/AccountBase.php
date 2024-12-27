<?php

namespace App\Models\ModelBase;

use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
class AccountBase extends ModelBase
{
	use HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'accounts';

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
		'user_id',
	];

	/**
	 * The hidden attributes
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'account_type',
		'user_id',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'initial_balance' => 'decimal:2',
	];

	protected $appends = [
		'type'
	];

	/**
	 * @return string[]
	 */
	public static function keys(): array
	{
		return [
			'id',
			'name',
			'description',
			'initial_balance' => 'decimal:2',
			'decimal_precision',
			'type',
		];
	}

	public function getInitialBalanceAttribute($value)
	{
		return $value / (10 ** $this->decimal_precision);
	}

	public function setInitialBalanceAttribute($value)
	{
		$this->attributes['initial_balance'] = $value * (10 ** $this->decimal_precision);
	}

	protected function getTypeAttribute()
	{
		return $this->account_type;
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
