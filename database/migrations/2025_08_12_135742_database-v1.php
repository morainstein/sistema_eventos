<?php

use App\Enums\UserRoleEnum;
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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->foreignId('id_role')->references('id')->on('roles');
            $table->enum('role', [UserRoleEnum::ADMIN,UserRoleEnum::PROMOTER,UserRoleEnum::CUSTOMER])
                ->comment("User role: ".UserRoleEnum::ADMIN->value.", ".UserRoleEnum::PROMOTER->value.", ".UserRoleEnum::CUSTOMER->value."");
            $table->string('phone',20)->unique();
            $table->string('registry', 20)->unique()
                ->comment('Registry number: CPF or CNPJ');
            $table->string('password');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('promoter_id')
                ->references('id')
                ->on('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_dateTime');
            $table->dateTime('end_dateTime');
            $table->string('banner_link')->nullable();
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');
            $table->integer('batch', false, true);
            $table->float('price', 8, 2);
            $table->integer('tickets_qty',false, true);
            $table->integer('tickets_sold',false, true)->default(0);
            $table->dateTime('end_dateTime');
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('batch_id')
                ->references('id')
                ->on('batches');
            $table->foreignUuid('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
        });

        // Schema::create('discounts', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('events');
        Schema::dropIfExists('users');
        Schema::dropIfExists('discounts');
    }
};
