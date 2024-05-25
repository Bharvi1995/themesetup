<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('residential_address')->after('business_address1')->nullable();
            $table->decimal('monthly_volume',10,2)->after('website_url')->default('0.00');
            $table->string('domain_ownership')->after('moa_document')->nullable();
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
            $table->dropColumn(['domain_ownership','residential_address','monthly_volume']);
        });
    }
}
