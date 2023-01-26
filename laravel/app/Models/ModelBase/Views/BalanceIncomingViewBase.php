<?php

namespace App\Models\ModelBase\Views;

use App\Models\Account;
use App\Models\Category;
use App\Models\FromTo;
use App\Models\ModelBase\ModelBase;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TransactionBase
 * @package App\Models\ModelBase
 *
 * @property integer $user_id
 * @property integer $account_id
 * @property string $transaction_type
 * @property integer $from_to_id
 * @property integer $category_id
 * @property integer $payment_type_id
 * @property integer $amount
 * @property integer $status
 * @property string $date_due
 * @property User $user
 * @property Account $account
 * @property FromTo $from_to
 * @property Category $category
 * @property PaymentType $payment_type
 */
class BalanceIncomingViewBase extends ModelBase
{
	use HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'balance_receipts';


	protected function getAmountAttribute($amount)
	{
		return $amount / (10 ** $this->account->decimal_precision);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	public function from_to()
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
