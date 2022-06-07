<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->default(0);
            $table->integer('adv_id')->default(0);
            $table->integer('aff_id')->default(0);
            $table->longText('campaigns_id');
            $table->longText('filename');
            $table->string('extension')->default('.csv');
            $table->string('separator')->default(',');
            $table->string('encryption')->default('not_defined'); // $columns_nb:hash_type, like 0:2-0:0-0:1 -> 0 : None, 1 : MD5, 2 : SHA1
            $table->string('header')->default('empty');
            $table->integer('nb_columns')->default(0);
            $table->integer('nb_lines')->default(0);
            $table->integer('content_length')->default(0);
            $table->string('path')->default('not_uploaded');
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
        Schema::dropIfExists('blacklists');
    }
}
