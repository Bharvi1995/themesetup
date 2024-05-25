<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIsDeleteColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('articles_categories', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('articles_tags', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('middetails', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('payout_schedule', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('rules', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('sub_users', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('technology_partners', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_delete');
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
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('agents', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('articles_categories', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('articles_tags', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('middetails', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('payout_schedule', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('rules', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('sub_users', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('technology_partners', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->enum('is_delete', [0, 1])->default(0);
        });
    }
}
