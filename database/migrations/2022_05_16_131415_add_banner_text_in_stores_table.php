<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerTextInStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->text('banner_text_1')->nullable()->after('description');
            $table->text('banner_text_2')->nullable()->after('banner_text_1');
            $table->text('banner_text_3')->nullable()->after('banner_text_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['banner_text_1','banner_text_2','banner_text_3']);
        });
    }
}
