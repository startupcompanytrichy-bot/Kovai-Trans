<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class ExpenseAccessory extends Model
{
    use BelongsToBranch;

    protected $table = 'expense_accessories';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'expense_id', 'accessory_name',
        'quantity', 'price', 'amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'decimal:2',
        'amount'   => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}
