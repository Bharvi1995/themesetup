<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyRegisteredNumberToRpApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rp_applications', function (Blueprint $table) {
            $table->string('company_registered_number')->nullable()->after('website_url');
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
            $table->dropColumn('company_registered_number');
        });
    }
}
