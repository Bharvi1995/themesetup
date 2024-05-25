<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->index('admin_id');
            $table->bigInteger('action_id')->index('action_id');
            $table->integer('actionvalue')->nullable()->index('actionvalue');
            $table->text('request')->nullable();
            $table->string('ip',255);
            $table->string('remark',255)->nullable();
            $table->timestamp('created_at')->nullable()->index('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable()->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_logs');
    }
}
