<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingAccount extends Model
{
    use HasFactory;

    protected $table = 'accounting_accounts';

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'parent_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AccountingAccount::class, 'parent_id')->orderBy('code');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(AccountingTransactionLine::class, 'accounting_account_id');
    }

    /**
     * Hitung saldo berjalan akun saat ini
     */
    public function getCurrentBalanceAttribute(): int
    {
        $totalDebit = (int) $this->lines()->sum('debit');
        $totalCredit = (int) $this->lines()->sum('credit');

        // Saldo normal Aset & Beban adalah Debit
        if (in_array($this->type, ['asset', 'expense'])) {
            return $totalDebit - $totalCredit;
        }

        // Saldo normal Kewajiban, Ekuitas, & Pendapatan adalah Kredit
        return $totalCredit - $totalDebit;
    }

    public function getFormattedBalanceAttribute(): string
    {
        $balance = $this->current_balance;
        $prefix = $balance < 0 ? '-Rp ' : 'Rp ';

        return $prefix.number_format(abs($balance), 0, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'asset' => '1 - Aset / Harta',
            'liability' => '2 - Kewajiban / Utang',
            'equity' => '3 - Ekuitas / Modal',
            'revenue' => '4 - Pendapatan / Pemasukan',
            'expense' => '5 - Beban / Pengeluaran',
            default => ucfirst($this->type),
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'asset' => 'bg-blue-50 text-blue-700 border-blue-200',
            'liability' => 'bg-amber-50 text-amber-700 border-amber-200',
            'equity' => 'bg-purple-50 text-purple-700 border-purple-200',
            'revenue' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'expense' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'cash_and_bank' => 'Kas & Bank',
            'current_asset' => 'Aset Lancar Lainnya',
            'fixed_asset' => 'Aset Tetap (Peralatan)',
            'current_liability' => 'Utang Lancar',
            'equity' => 'Modal Organisasi',
            'operational_revenue' => 'Pendapatan Rutin Operasional',
            'project_revenue' => 'Pendapatan Kegiatan / Proyek',
            'grant_revenue' => 'Bantuan & Sponsorship',
            'operational_expense' => 'Beban Operasional Markas',
            'project_expense' => 'Beban Kegiatan / Proyek',
            'admin_expense' => 'Beban Kesekretariatan & ATK',
            default => ucwords(str_replace('_', ' ', $this->category)),
        };
    }
}
