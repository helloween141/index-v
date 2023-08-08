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
        Schema::create('vpanel_user_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('model');
            $table->string('app_version');
            $table->string('pin')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('need_to_set_pin')->default(true);
            $table->string('auth_token')->nullable();
            $table->string('session_token')->nullable();
            $table->foreignId('user')->constrained('vpanel_users')->cascadeOnDelete();
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
        Schema::dropIfExists('vpanel_user_devices');
    }
};
