<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vpanel_cache_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity')->constrained('vpanel_rule_entities')->cascadeOnDelete();
            $table->integer('record_id');
            $table->integer('user')->constrained('users')->cascadeOnDelete();
            $table->integer('permission');
            $table->unique(['entity', 'record_id', 'user'], 'cache_row_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vpanel_cache_permissions');
    }
};
