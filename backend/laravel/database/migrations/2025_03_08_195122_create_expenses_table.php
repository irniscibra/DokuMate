<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); 
            $table->decimal('amount', 10, 2); 
            $table->string('category'); 
            $table->text('description')->nullable(); 
            $table->date('date'); // Datum der Ausgabe
            $table->boolean('recurring')->default(false); // Wiederkehrend (monatlich, jährlich)
            $table->string('attachment')->nullable(); // Beleg (Datei-Upload)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
