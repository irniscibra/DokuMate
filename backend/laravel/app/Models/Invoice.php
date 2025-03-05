<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'client_id',
        'user_id',
        'invoice_number',
        'invoice_date',
        'total_amount',
        'status',
    ];

 
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Beziehung zu Client 
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Beziehung zu User (Wer hat die Rechnung erstellt?)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
{
    return $this->hasMany(InvoiceItem::class);
}
}
