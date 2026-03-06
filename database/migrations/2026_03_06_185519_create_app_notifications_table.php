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
        Schema::create('app_notifications', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('domain');
            $table->string('oauth_client_id');
            $table->timestamps();

            //
            $table->bigInteger('pay_account_id');
            $table->bigInteger('local_branch_id')->nullable();
            $table->string('local_system_name')->nullable();

            //
            $table->integer('notice_count')->default(0);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
