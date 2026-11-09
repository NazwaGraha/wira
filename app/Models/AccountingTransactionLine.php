<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingTransactionLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_transaction_lines';

    protected $fillable = [
        'accounting_transaction_id',
        'accounting_account_id',
        'debit',
        'credit',
        'memo',
    ];

    protected $casts = [
        'debit' => 'integer',
        'credit' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(AccountingTransaction::class, 'accounting_transaction_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'accounting_account_id');
    }
}
