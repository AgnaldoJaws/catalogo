<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_loc_id')->constrained('business_locations')->cascadeOnDelete();
            $table->tinyInteger('weekday');      // 0=dom ... 6=sáb
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('overnight')->default(false); // cruza meia-noite?
            $table->timestamps();

            $table->unique(['business_loc_id','weekday','open_time','close_time'], 'uq_loc_day_range');
        });
    }
    public function down(): void {
        Schema::dropIfExists('business_hours');
    }
};
