<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentdToRpApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rp_applications', function (Blueprint $table) {
            $table->text('passport')->nullable()->after('generated_lead');
            $table->text('utility_bill')->nullable()->after('passport');
            $table->text('company_incorporation_certificate')->nullable()->after('utility_bill');
            $table->text('tax_id')->nullable()->after('company_incorporation_certificate');
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
            $table->dropColumn('passport');
            $table->dropColumn('utility_bill');
            $table->dropColumn('company_incorporation_certificate');
            $table->dropColumn('tax_id');
        });
    }
}
