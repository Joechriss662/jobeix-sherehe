<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->datetime('start_time');
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->integer('capacity')->nullable();
            $table->enum('status',[
                'upcoming',
                'ongoing',
                'completed',
                'cancelled'
            ])->default('upcoming');
            $table->string('event_code')->unique();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
