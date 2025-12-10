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
    Schema::create('curhats', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable();
        $table->text('message');
        $table->boolean('anonymous')->default(true);
        $table->string('category')->nullable();
        $table->enum('status', ['open','answered','closed'])->default('open');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curhats');
    }
};
