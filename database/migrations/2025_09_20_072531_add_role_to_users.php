<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Role do usuário: admin, owner, staff
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('staff')->after('email'); // staff por padrão
            }
        });

        // Pivot de times (usuário x negócio)
        if (!Schema::hasTable('business_user')) {
            Schema::create('business_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedBigInteger('user_id');
                $table->string('role', 20)->default('staff'); // owner|manager|staff (granularidade dentro do negócio)
                $table->timestamps();

                $table->unique(['business_id', 'user_id']);
                $table->foreign('business_id')->references('id')->on('businesses')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('business_user');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
