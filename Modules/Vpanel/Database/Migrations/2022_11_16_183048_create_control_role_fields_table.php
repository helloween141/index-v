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
        Schema::create('vpanel_control_role_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role')->constrained('vpanel_roles')->cascadeOnDelete();
            $table->foreignId('entity')->constrained('vpanel_rule_entities')->cascadeOnDelete();
            $table->string('field');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vpanel_control_role_fields');
    }
};
