<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeCreativeOfferUrlLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //not functioning
        // DB::query('ALTER TABLE creative_uploads MODIFY `offer_preview_url` VARCHAR(355)');
        // DB::query('ALTER TABLE creative_uploads MODIFY `offer_url` VARCHAR(355)');

        Schema::table('creative_uploads', function($table)
        {
            $table->string('offer_preview_url', 355)->change();
            $table->string('offer_url', 355)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::query('ALTER TABLE creative_uploads MODIFY `offer_preview_url` VARCHAR(255)');
        // DB::query('ALTER TABLE creative_uploads MODIFY `offer_url` VARCHAR(255)');

        Schema::table('creative_uploads', function($table)
        {
            $table->string('offer_preview_url', 255)->change();
            $table->string('offer_url', 255)->change();
        });
    }
}
