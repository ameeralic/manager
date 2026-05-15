<?php

namespace App\Models;

use Database\Factories\ExpenseTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExpenseTag extends Model
{
    /** @use HasFactory<ExpenseTagFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ExpenseCategory::class, 'expense_category_expense_tag');
    }

    public function expenses(): BelongsToMany
    {
        return $this->belongsToMany(Expense::class, 'expense_expense_tag');
    }
}
