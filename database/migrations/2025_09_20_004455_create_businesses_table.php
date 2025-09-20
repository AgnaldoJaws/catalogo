<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->text('about')->nullable();
            $table->decimal('avg_rating', 2, 1)->default(0);
            $table->unsignedInteger('items_count')->default(0);
            $table->tinyInteger('status')->default(1); // 1=ativo
            $table->timestamps();

            $table->index(['status','avg_rating']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('businesses');
    }
};
