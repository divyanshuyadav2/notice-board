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
        Schema::create('admn_tran_ntic_share', function (Blueprint $table) {

        $table->bigIncrements('Share_UIN');

        $table->unsignedBigInteger('Ntic_Crcl_UIN');
        $table->string('Share_Token', 64)->unique();

        $table->timestamp('Expires_At');
        $table->timestamp('Created_At')->useCurrent();

        $table->unsignedBigInteger('Created_By')->nullable();

        $table->boolean('Is_Active')->default(true);

        $table->ipAddress('Created_IP')->nullable();
        $table->unsignedInteger('Access_Count')->default(0);

        $table->index('Share_Token');
        $table->index(['Ntic_Crcl_UIN', 'Is_Active']);

    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admn_tran_ntic_share');
    }
};
