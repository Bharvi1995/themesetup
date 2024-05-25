<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Country name');
            $table->string('full_name')->comment('Full Country Name');
            $table->string('code', 2)->comment('Two-letter country code (ISO 3166-1 alpha-2)');
            $table->string('iso3', 3)->comment('Three-letter country code (ISO 3166-1 alpha-3)');
            $table->string('number', 3)->comment('Three-digit country number (ISO 3166-1 numeric)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
