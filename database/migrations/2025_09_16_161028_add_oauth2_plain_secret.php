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
        //
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->text('plain_secret')->nullable();
        }); 
        
        \DB::unprepared('
        UPDATE oauth_clients SET plain_secret = secret WHERE id IS NOT NULL
        ');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
