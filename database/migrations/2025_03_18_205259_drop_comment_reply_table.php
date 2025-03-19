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
        Schema::dropIfExists('comment_reply');
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('comment_reply', function (Blueprint $table){
            $table->id();
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->foreignId('reply_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
};
