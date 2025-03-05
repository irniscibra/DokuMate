<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('invoice_number')->unique(); 
            $table->date('invoice_date'); // 
            $table->decimal('total_amount', 10, 2); // 
            $table->string('status')->default('offen'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
