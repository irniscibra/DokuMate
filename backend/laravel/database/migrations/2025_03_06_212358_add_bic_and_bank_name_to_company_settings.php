<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('bic')->nullable()->after('bank_details');
            $table->string('bank_name')->nullable()->after('bic');
        });
    }

    public function down() {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['bic', 'bank_name']);
        });
    }
};