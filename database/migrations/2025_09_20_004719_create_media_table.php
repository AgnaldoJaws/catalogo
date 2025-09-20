<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('kind', ['logo','cover','gallery']);
            $table->string('url');
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['business_id','kind','sort_order']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('media');
    }
};
