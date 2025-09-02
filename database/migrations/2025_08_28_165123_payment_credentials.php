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
        Schema::create('paggue_credentials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('promoter_id')->references('id')->on('users');
            $table->string('company_id')->nullable();
            $table->text('webhook_token')->nullable();
            $table->integer('webhook_id',false,true)->nullable();
            $table->text('bearer_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paggue_credentials');
    }
};
