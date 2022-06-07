<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreativeUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creative_uploads', function (Blueprint $table) {
            $table->id();
            $table->integer('ho_lp_id')->default(0);
            $table->integer('ho_creative_id')->default(0);
            $table->string('filename')->default('unnamed');
            $table->string('original_name')->default('noname');
            $table->string('title')->default('untitled');
            $table->integer('campaign_number')->default(0);
            $table->integer('img_uploaded')->default(0);
            $table->integer('length')->default(0);
            $table->longText('code');
            $table->string('start_date')->default('empty');
            $table->string('end_date')->default('empty');
            $table->string('crea_test_url')->default('unavailable');
            $table->string('crea_display_name')->default('unnamed');
            $table->integer('crea_number_in_offer')->default(0);
            $table->integer('lp_number_in_offer')->default(0);
            $table->string('offer_url')->default('empty');
            $table->string('offer_preview_url')->default('empty');
            $table->string('offer_tracking_url')->default('empty');
            $table->string('doctype')->default('empty');
            $table->integer('upload_user_id')->default(0);
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
        Schema::dropIfExists('creative_uploads');
    }
}
