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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id'); // the user who referred
            $table->unsignedBigInteger('referee_id');  // the new user
            $table->tinyInteger('level')->default(1);  // referral level, default to 1
            $table->timestamps();

            // Foreign keys
            $table->foreign('referral_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referee_id')->references('id')->on('users')->onDelete('cascade');

            // Ensure uniqueness of referral relationship
            $table->unique(['referral_id', 'referee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
