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
        Schema::table('guests', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->foreignId('invitation_id')->nullable()->constrained('invitations')->onDelete('cascade');
            $table->enum('rsvp_status', ['pending','accepted','declined'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropForeign(['invitation_id']);
            $table->dropColumn('rsvp_status');
        });
    }
};
