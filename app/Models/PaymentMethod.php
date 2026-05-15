<?php

namespace App\Models;

use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    /** @use HasFactory<PaymentMethodFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name', 'paid_by'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
