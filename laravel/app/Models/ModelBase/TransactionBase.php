<?php

namespace App\Models\ModelBase;

use App\Models\Account;
use App\Models\Category;
use App\Models\FromTo;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionBase
 * @package App\Models\ModelBase
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $account_id
 * @property string $transaction_type
 * @property string $description
 * @property integer $from_id
 * @property integer $to_id
 * @property integer $category_id
 * @property integer $payment_type_id
 * @property integer $value
 * @property integer $status
 * @property string $date_due
 * @property string $date_payment
 * @property User $user
 * @property Account $account
 * @property FromTo $from_to
 * @property Category $category
 * @property PaymentType $payment_type
 */
class TransactionBase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

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
        'account_id',
        'transaction_type',
        'description',
        'from_id',
        'to_id',
        'category_id',
        'payment_type_id',
        'value',
        'status',
        'date_due',
        'date_payment',
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

	public function labels(){
		return ['status' => 'paid'];
	}

	protected function status(): Attribute
	{
		return Attribute::make(
			get: static fn ($value) => $value ? 'Yes' : 'No'
		);
	}

    /**
     * @return string[]
     */
    public static function keys(): array
    {
        return [
            'user_id',
            'account_id',
            'transaction_type',
            'description',
            'from_id',
            'to_id',
            'category_id',
            'payment_type_id',
            'value',
            'status',
            'date_due',
            'date_payment',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function from()
    {
        return $this->belongsTo(FromTo::class);
    }

	public function to()
	{
		return $this->belongsTo(FromTo::class);
	}

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

}
