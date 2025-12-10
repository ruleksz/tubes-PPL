<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('curhat_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('curhat_id')->constrained('curhats')->onDelete('cascade');
        $table->string('sender')->default('user'); // 'user' or 'ai' or 'admin'
        $table->text('message');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curhat_messages');
    }
};
