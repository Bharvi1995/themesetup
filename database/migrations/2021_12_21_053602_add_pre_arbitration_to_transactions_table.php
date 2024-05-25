<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreArbitrationToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('is_pre_arbitration',['0','1'])->after('is_duplicate_delete')->default('0');
            $table->timestamp('pre_arbitration_date')->after('is_pre_arbitration')->nullable();
            $table->string('pre_arbitration_sent_files')->after('pre_arbitration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['is_pre_arbitration','pre_arbitration_date','pre_arbitration_sent_files']);
        });
    }
}
