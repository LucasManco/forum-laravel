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
        Schema::create('answer_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('answer_id');
            $table->foreign('answer_id')->references('id')
                ->on('answers')->onDelete('cascade');
            $table->string('content');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_attachments');
    }
};
