<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('businesses', function (Blueprint $t) {
            $t->string('logo_url')->nullable()->change();   // se já existir, ok
            $t->string('logo_path')->nullable()->after('logo_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('businesses', function (Blueprint $t) {
            $t->dropColumn('logo_path');
        });
    }
};
