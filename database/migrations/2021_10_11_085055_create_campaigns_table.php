<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id')->default(0);
            $table->string('name')->default('unnamed');
            $table->longText('default_offer_url');
            $table->longText('default_preview_url');
            $table->string('status')->default('');
            $table->integer('redirect_offer_id')->default(0);
            $table->integer('has_goals_enabled')->default(0);
            $table->longText('description');
            $table->longText('email_instructions');
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
        Schema::dropIfExists('campaigns');
    }
}
