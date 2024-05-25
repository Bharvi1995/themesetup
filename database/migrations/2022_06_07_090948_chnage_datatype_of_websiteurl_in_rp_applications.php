<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChnageDatatypeOfWebsiteurlInRpApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rp_applications', function (Blueprint $table) {
            $table->text('website_url')->nullable()->change();
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
            $table->string('website_url')->nullable()->change();
        });
    }
}
