<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->integer('worked_hours')->nullable(); // Arbeitsstunden
            $table->decimal('cost', 10, 2)->nullable(); // Kosten in €
            $table->boolean('billed')->default(0); // Wurde Rechnung erstellt?
            $table->text('report')->nullable(); // Bericht zum Termin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
        });
    }
};
