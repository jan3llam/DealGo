<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('port_id')->nullable()->constrained('ports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('requests')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('goods_id')->nullable()->constrained('goods_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('stowage_factor')->nullable();
            $table->string('min_weight')->nullable();
            $table->string('max_weight')->nullable();
            $table->integer('cbm_cbft')->default(false);
            $table->string('min_cbm_cbft')->nullable();
            $table->string('max_cbm_cbft')->nullable();
            $table->string('min_sqm')->nullable();
            $table->string('max_sqm')->nullable();
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
        Schema::dropIfExists('load_requests');
    }
}
