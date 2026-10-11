<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::observe(\App\Observers\TransactionObserver::class);
    }

    protected $table = 'transactions';

    public const TYPE_RECEI = 'RECEI';

    public const TYPE_FIXEX = 'FIXEX';

    public const TYPE_VAREX = 'VAREX';

    public const TYPE_PEOPL = 'PEOPL';

    public const TYPE_TAXES = 'TAXES';

    public const TYPE_TRANS = 'TRANS';

    public const TYPES = [
        self::TYPE_RECEI,
        self::TYPE_FIXEX,
        self::TYPE_VAREX,
        self::TYPE_PEOPL,
        self::TYPE_TAXES,
        self::TYPE_TRANS,
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'from_to_id',
        'payment_type_id',
        'transaction_type',
        'description',
        'amount',
        'status',
        'date_due',
        'date_payment',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'category_id' => 'integer',
        'from_to_id' => 'integer',
        'payment_type_id' => 'integer',
        'amount' => 'integer',
        'status' => 'boolean',
        'date_due' => 'datetime',
        'date_payment' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function fromTo(): BelongsTo
    {
        return $this->belongsTo(FromTo::class, 'from_to_id');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function getAmountAttribute($value): float
    {
        $precision = $this->account?->decimal_precision ?? 2;

        return $value / pow(10, $precision);
    }

    public function setAmountAttribute($value): void
    {
        $precision = $this->account?->decimal_precision ?? 2;
        $this->attributes['amount'] = (int) round($value * pow(10, $precision));
    }

    public function getRawAmountAttribute(): int
    {
        return $this->attributes['amount'] ?? 0;
    }

    public function isTransfer(): bool
    {
        return $this->transaction_type === self::TYPE_TRANS;
    }

    /**
     * Perna oposta da transferência (saída <-> entrada), ou null quando não há par.
     * Casa pelos valores crus do banco; sem par exato, tenta mesma descrição/data em outra conta.
     */
    public function pairedTransfer(): ?self
    {
        if (! $this->isTransfer()) {
            return null;
        }

        $rawAmount = (int) $this->getRawOriginal('amount');
        $day = Carbon::parse($this->getRawOriginal('date_due'))->format('Y-m-d');

        $candidates = static::query()
            ->where('user_id', $this->user_id)
            ->where('transaction_type', self::TYPE_TRANS)
            ->where('description', $this->getRawOriginal('description'))
            ->whereRaw('DATE(date_due) = ?', [$day])
            ->where('id', '!=', $this->id);

        $exact = (clone $candidates)
            ->whereRaw('ABS(amount) = ?', [abs($rawAmount)])
            ->whereRaw('(amount * ?) < 0', [$rawAmount])
            ->first();

        return $exact ?? (clone $candidates)->where('account_id', '!=', $this->account_id)->first();
    }

    /**
     * @return array{month_balance: float, final_balance: float, final_balance_transfer: float}
     */
    public static function getFullBalance(string $dateFilter, string $accountFilter = 'all', string $paidFilter = 'all', bool $backwardsBalance = false): array
    {
        return (new TransactionReport(auth()->id()))->fullBalance($dateFilter, $accountFilter, $paidFilter, $backwardsBalance);
    }

    /**
     * @return array{total: float, accounts: array<int, float>}
     */
    public static function finalBalancesByAccount(string $dateFilter, string $paidFilter = '1'): array
    {
        return (new TransactionReport(auth()->id()))->finalBalancesByAccount($dateFilter, $paidFilter);
    }

    public static function getMonthTotals(string $dateFilter): array
    {
        return (new TransactionReport(auth()->id()))->monthTotals($dateFilter);
    }

    public static function groupTransfers($transfers): array
    {
        return TransactionReport::groupTransfers($transfers);
    }

    public static function getAmountsPaginated(string $dateFilter, string $accountFilter, string $paidFilter, int $perPage = 15): array
    {
        return (new TransactionReport(auth()->id()))
            ->amountsPaginated($dateFilter, $accountFilter, $paidFilter, $perPage, (int) request()->input('page', 1));
    }

    public static function keys(): array
    {
        return ['id', 'user_id', 'account_id', 'category_id', 'from_to_id', 'payment_type_id', 'transaction_type', 'description', 'amount', 'status', 'date_due', 'date_payment'];
    }
}
