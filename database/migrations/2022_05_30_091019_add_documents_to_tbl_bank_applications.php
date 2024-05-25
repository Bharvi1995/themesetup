<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentsToTblBankApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_applications', function (Blueprint $table) {
            $table->text('passport')->nullable()->after('descriptors');
            $table->text('latest_bank_account_statement')->nullable()->after('passport');
            $table->text('utility_bill')->nullable()->after('latest_bank_account_statement');
            $table->text('company_incorporation_certificate')->nullable()->after('utility_bill');
            $table->text('article_of_accociasion')->nullable()->after('company_incorporation_certificate');
            $table->text('tax_certificate')->nullable()->after('article_of_accociasion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_applications', function (Blueprint $table) {
            $table->dropColumn('passport');
            $table->dropColumn('latest_bank_account_statement');
            $table->dropColumn('utility_bill');
            $table->dropColumn('company_incorporation_certificate');
            $table->dropColumn('article_of_accociasion');
            $table->dropColumn('tax_certificate');
        });
    }
}
