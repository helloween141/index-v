<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vpanel_subordinates_structure', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('title')->default('');
            $table->string('type')->default('');
            $table->string('user_interface')->nullable()->default('default');
            $table->foreignId('parent')->nullable()->constrained('vpanel_subordinates_structure')->cascadeOnDelete();
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
        Schema::dropIfExists('vpanel_subordinates_structure');
    }
};
