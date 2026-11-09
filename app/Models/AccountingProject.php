<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingProject extends Model
{
    use HasFactory;

    protected $table = 'accounting_projects';

    protected $fillable = [
        'code',
        'name',
        'category',
        'status',
        'start_date',
        'end_date',
        'budget_target_revenue',
        'budget_limit_expense',
        'person_in_charge',
        'treasurer_name',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget_target_revenue' => 'integer',
        'budget_limit_expense' => 'integer',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountingTransaction::class, 'accounting_project_id');
    }

    /**
     * Total Pemasukan Proyek (Berdasarkan transaksi kas masuk / pendapatan yang ditautkan)
     */
    public function getTotalRevenueAttribute(): int
    {
        return (int) AccountingTransactionLine::whereHas('transaction', function ($q) {
            $q->where('accounting_project_id', $this->id)->where('status', 'posted');
        })->whereHas('account', function ($q) {
            $q->where('type', 'revenue');
        })->sum('credit');
    }

    /**
     * Total Pengeluaran Proyek (Berdasarkan transaksi belanja / beban yang ditautkan)
     */
    public function getTotalExpenseAttribute(): int
    {
        return (int) AccountingTransactionLine::whereHas('transaction', function ($q) {
            $q->where('accounting_project_id', $this->id)->where('status', 'posted');
        })->whereHas('account', function ($q) {
            $q->where('type', 'expense');
        })->sum('debit');
    }

    /**
     * Sisa Kas / Surplus Defisit Bersih Proyek
     */
    public function getNetSurplusAttribute(): int
    {
        return $this->total_revenue - $this->total_expense;
    }

    public function getFormattedRevenueAttribute(): string
    {
        return 'Rp '.number_format($this->total_revenue, 0, ',', '.');
    }

    public function getFormattedExpenseAttribute(): string
    {
        return 'Rp '.number_format($this->total_expense, 0, ',', '.');
    }

    public function getFormattedNetSurplusAttribute(): string
    {
        $surplus = $this->net_surplus;
        $prefix = $surplus < 0 ? '-Rp ' : '+Rp ';

        return $prefix.number_format(abs($surplus), 0, ',', '.');
    }

    public function getRevenueRealizationPercentAttribute(): float
    {
        if ($this->budget_target_revenue <= 0) {
            return 100.0;
        }

        return round(($this->total_revenue / $this->budget_target_revenue) * 100, 1);
    }

    public function getExpenseAbsorptionPercentAttribute(): float
    {
        if ($this->budget_limit_expense <= 0) {
            return 0.0;
        }

        return round(($this->total_expense / $this->budget_limit_expense) * 100, 1);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'Perencanaan',
            'active' => 'Aktif / Berjalan',
            'completed' => 'Selesai (LPJ Terbit)',
            'archived' => 'Diarsipkan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'bg-amber-50 text-amber-700 border-amber-200',
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
            'archived' => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'competition' => 'Perlombaan (Event SBB)',
            'diklat' => 'Diklat & Pelantikan Anggota',
            'social_service' => 'Aksi Sosial / Donor Darah',
            'routine_operational' => 'Operasional Rutin BPH',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }
}
