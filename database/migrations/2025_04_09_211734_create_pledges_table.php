<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Use UUID for primary key
            $table->uuid('event_id'); // Foreign key to events table
            $table->uuid('guest_id'); // Foreign key to guests table
            $table->enum('type', ['cash', 'bank_transfer', 'service', 'gift']);
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'partially_fulfilled', 'fulfilled', 'overdue'])->default('pending');
            $table->date('deadline')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_frequency')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pledges');
    }
};
