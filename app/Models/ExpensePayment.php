<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class ExpensePayment extends Model
{
    use BelongsToBranch;

    protected $table = 'expense_payments';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'expense_id', 'payment_date', 'amount',
        'payment_mode', 'reference_no', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
