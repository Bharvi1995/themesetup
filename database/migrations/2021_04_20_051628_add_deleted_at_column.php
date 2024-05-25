<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('articles_categories', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('articles_tags', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('middetails', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('payout_schedule', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('rules', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('sub_users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('technology_partners', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('articles_categories', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('articles_tags', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('middetails', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('payout_schedule', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('rules', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('sub_users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('technology_partners', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
