<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingTransaction extends Model
{
    use HasFactory;

    protected $table = 'accounting_transactions';

    protected $fillable = [
        'voucher_number',
        'type',
        'transaction_date',
        'accounting_project_id',
        'recipient_or_payer',
        'description',
        'amount',
        'reference_number',
        'attachment_path',
        'created_by_user_id',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(AccountingProject::class, 'accounting_project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getCreatorNameAttribute(): string
    {
        if ((int) $this->created_by_user_id === 999999999) {
            return 'Super Administrator';
        }
        if ((int) $this->created_by_user_id === 888888888) {
            return 'Administrator';
        }

        return $this->creator?->name ?? 'Admin PMR';
    }

    public function lines(): HasMany
    {
        return $this->hasMany(AccountingTransactionLine::class, 'accounting_transaction_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'cash_in' => 'Kas Masuk (BKM)',
            'cash_out' => 'Kas Keluar (BKK)',
            'transfer' => 'Transfer Kas (TRF)',
            'journal' => 'Jurnal Penyesuaian',
            default => strtoupper($this->type),
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'cash_in' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'cash_out' => 'bg-rose-50 text-rose-700 border-rose-200',
            'transfer' => 'bg-blue-50 text-blue-700 border-blue-200',
            'journal' => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    /**
     * Generate Voucher Number Otomatis (Accurate Style)
     */
    public static function generateVoucherNumber(string $type, $date = null): string
    {
        $prefix = match ($type) {
            'cash_in' => 'BKM',
            'cash_out' => 'BKK',
            'transfer' => 'TRF',
            default => 'JRN',
        };

        $year = $date ? Carbon::parse($date)->format('Y') : date('Y');

        $latest = self::where('voucher_number', 'LIKE', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->voucher_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $nextNumber);
    }
}
