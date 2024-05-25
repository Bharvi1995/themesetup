<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsProvideRefundToMiddetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->integer('per_day_card')->default(3)->after('per_day_limit');
            $table->integer('per_day_email')->default(3)->after('per_day_card');
            $table->integer('per_week_card')->default(3)->after('per_day_email');
            $table->integer('per_week_email')->default(3)->after('per_week_card');
            $table->integer('per_month_card')->default(6)->after('per_week_email');
            $table->integer('per_month_email')->default(6)->after('per_month_card');
            $table->enum('is_provide_refund',['0','1'])->after('farma_mid')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->dropColumn(['is_provide_refund','per_month_email','per_month_card','per_week_email','per_week_card','per_day_email','per_day_card']);
        });
    }
}
