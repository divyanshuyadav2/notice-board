<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admn_tran_ntic_crcl', function (Blueprint $table) {

            /* ================= PRIMARY KEY ================= */
            $table->bigIncrements('Ntic_Crcl_UIN'); // AUTO INCREMENT UNIQUE



            /* ================= BASIC INFO ================= */
            $table->string('Orga_Name');                      // Orga_Name
            $table->date('Ntic_Crcl_Dt');                     // Ntic_Crcl_Dt
            $table->string('Subj');                           // Subj
            $table->date('Eft_Dt')->nullable();               // Eft_Dt

            /* ================= CONTENT ================= */
            $table->longText('Cntn')->nullable();             // Cntn
            $table->string('Atch_Path')->nullable();          // Atch_Path
            $table->enum('mode', ['draft', 'attachment']);    // mode

            /* ================= SIGNATORY ================= */
            $table->string('Imgs_Sgnt')->nullable();          // Imgs_Sgnt
            $table->string('Athr_Pers_Name')->nullable();     // Athr_Pers_Name
            $table->string('Dsig')->nullable();               // Dsig
            $table->string('Dept')->nullable();               // Dept

            /* ================= STATUS & TYPE ================= */
            $table->enum('Stau', ['draft', 'published'])->default('draft');                         // Stau
            $table->string('Docu_Type');                      // Docu_Type

            /* ================= AUDIT FIELDS ================= */
            $table->unsignedBigInteger('CrBy')->default(1);  // CrBy
            $table->timestamp('CrOn')->useCurrent();         // CrOn

            $table->unsignedBigInteger('MoBy')->nullable();  // MoBy
            $table->timestamp('MoOn')->nullable();           // MoOn

            $table->unsignedBigInteger('Pbli_By')->nullable();// Pbli_By
            $table->timestamp('Pbli_On')->nullable();         // Pbli_On

            /* ================= INDEXES ================= */
            $table->index(['Stau', 'Docu_Type']);
            $table->index('Ntic_Crcl_Dt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admn_tran_ntic_crcl');
    }
};
