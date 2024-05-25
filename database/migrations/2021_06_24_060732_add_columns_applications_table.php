<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('country_code')->after('settlement_currency')->nullable();
            $table->string('other_processing_country')->after('processing_country')->nullable();
            $table->string('other_industry_type')->after('category_id')->nullable();
            $table->string('licence_document')->after('company_license')->nullable();
            $table->string('moa_document')->after('passport')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('other_processing_country');
            $table->dropColumn('other_industry_type');
            $table->dropColumn('licence_document');
            $table->dropColumn('moa_document');
        });
    }
}
