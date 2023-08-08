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
        Schema::create('vpanel_role_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('permission');
            $table->foreignId('entity')->constrained('vpanel_rule_entities')->cascadeOnDelete();
            $table->foreignId('role')->constrained('vpanel_roles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vpanel_role_rules');
    }
};
