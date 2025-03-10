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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titel der Aufgabe
            $table->text('description')->nullable(); // Beschreibung
            $table->foreignId('assigned_to')->constrained('users'); // Mitarbeiter-Zuweisung
            $table->enum('status', ['offen', 'in Bearbeitung', 'erledigt'])->default('offen'); // Status
            $table->date('due_date')->nullable(); // Fälligkeitsdatum
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
