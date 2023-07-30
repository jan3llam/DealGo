<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOthersToRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            // $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('vessel_category')->nullable();
            $table->string('vessel_category_json')->nullable();
            $table->boolean('prompt')->default(false);
            $table->boolean('spot')->default(false);
            $table->boolean('dead_spot')->default(false);
            $table->integer('sole_part')->nullable();
            $table->string('address_commission')->nullable();
            $table->string('broker_commission')->nullable();
            $table->string('part_type')->nullable();
            $table->string('min_weight')->nullable();
            $table->string('max_weight')->nullable();
            $table->string('min_cbm')->nullable();
            $table->string('max_cbm')->nullable();
            $table->string('min_cbft')->nullable();
            $table->string('max_cbft')->nullable();
            $table->string('min_sqm')->nullable();
            $table->string('max_sqm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            // $table->dropConstrainedForeignId('category_id');
            $table->dropColumn("vessel_category");
            $table->dropColumn("vessel_category_json");
            $table->dropColumn("prompt");
            $table->dropColumn("spot");
            $table->dropColumn("dead_spot");
            $table->dropColumn("address_commission");
            $table->dropColumn("broker_commission");
            $table->dropColumn("sole_part");
            $table->dropColumn("part_type");
            $table->dropColumn("min_weight");
            $table->dropColumn("max_weight");
            $table->dropColumn('min_cbm');
            $table->dropColumn('max_cbm');
            $table->dropColumn('min_cbft');
            $table->dropColumn('max_cbft');
            $table->dropColumn('min_sqm');
            $table->dropColumn('max_sqm');
        });
    }
}
