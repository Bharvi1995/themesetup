<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditWebsiteUrlMajorRegiousInRpApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rp_applications', function (Blueprint $table) {
            $table->string('website_url', 255)->nullable()->change();
            $table->text('major_regious')->nullable()->change();
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
            $table->string('website_url', 20)->nullable()->change();
            $table->string('major_regious')->nullable()->change();
        });
    }
}
