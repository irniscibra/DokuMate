<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', // Falls Ausgaben einer Firma zugeordnet sind
        'amount',
        'category',
        'description',
        'date',
        'recurring',
        'attachment',
    ];

    /**
     * Beziehung zur Firma
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
