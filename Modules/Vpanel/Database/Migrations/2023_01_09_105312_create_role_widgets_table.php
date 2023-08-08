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
        Schema::create('vpanel_role_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role')->constrained('vpanel_roles')->cascadeOnDelete();
            $table->foreignId('widget')->constrained('vpanel_widgets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vpanel_role_widgets');
    }
};
