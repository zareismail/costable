<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostableCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costable_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->constrained('costable_fees');
            $table->string('tracking_code')->nullable();
            $table->string('notes', 500)->nullable(); 
            $table->price('amount')->default(0.000); 
            $table->price('due')->default(0.000); 
            $table->timestamp('target_date')->nullalbe(); 
            $table->morphs('costable');
            $table->auth();
            $table->detail();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('costable_costs');
    }
}
