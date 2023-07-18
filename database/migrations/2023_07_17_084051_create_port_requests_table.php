<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('port_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('port_id')->nullable()->constrained('ports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('requests')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('geo_id')->nullable()->constrained('geo_areas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('port_type')->nullable();
            $table->string('confirme_type')->nullable();
            $table->string('port_sea')->nullable();
            $table->integer('sea_river')->nullable();
            $table->string('sea_draft')->nullable();
            $table->string('air_draft')->nullable();
            $table->string('beam_restriction')->nullable();
            $table->integer('loading_conditions')->nullable();
            $table->string('mtone_value')->nullable();
            $table->boolean('NAABSA')->default(false);
            $table->boolean('SSHINC')->default(false);
            $table->boolean('SSHEX')->default(false);
            $table->boolean('FHINC')->default(false);
            $table->boolean('FHEX')->default(false);
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
        Schema::dropIfExists('port_requests');
    }
}
