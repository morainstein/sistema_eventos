<?php

use App\Enums\DiscountType;
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
        Schema::create('discounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('coupon_code');
            $table->foreignUuid('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->enum('discount_type', [DiscountType::FIXED, DiscountType::PERCENTAGE])
                ->comment('Types: '. DiscountType::FIXED->value .', '. DiscountType::PERCENTAGE->value);
            $table->bigInteger('discount_amount')
                ->comment("Cents for fixeds discounts");
            $table->integer('usage_limit')->nullable();
            $table->integer('times_used')->default(0);
            $table->dateTime('valid_until');
            $table->timestamps();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignUuid('event_id')
                ->nullable()
                ->constrained('events')
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {        
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });

        Schema::dropIfExists('discounts');
    }
};
