<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneratedLeadToRpApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rp_applications', function (Blueprint $table) {
            $table->text('generated_lead')->nullable()->after('major_regious');
            $table->text('payment_solutions_needed')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rp_applications', function (Blueprint $table) {
            $table->dropColumn('generated_lead');
        });
    }
}
