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
            $table->foreignUuid('promoter_id')->references('id')->on('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('banner_link')->nullable();
        });

        Schema::create('batchs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_event')->references('id')->on('events');
            $table->integer('batch',false, true);
            $table->float('price', 8, 2);
            $table->integer('tickets_qty',false, true);//->nullable()->default(null);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_batch')->references('id')->on('batchs');
            $table->foreignUuid('id_user')->references('id')->on('users');
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
        Schema::dropIfExists('batchs');
        Schema::dropIfExists('events');
        Schema::dropIfExists('users');
        // Schema::dropIfExists('roles');
        Schema::dropIfExists('discounts');
    }
};
