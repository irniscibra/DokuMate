<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'address',
        'email',
        'phone',
        'tax_id',
        'ceo_name',
        'bank_details',
        'bic',     
        'bank_name',    
        'invoice_footer'
    ];
}
