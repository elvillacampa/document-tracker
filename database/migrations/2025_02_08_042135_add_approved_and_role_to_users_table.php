<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // By default, set approved to false so the user must be approved
            $table->boolean('approved')->default(false);
            // Adding a role column, with a default (e.g., viewer)
            $table->string('role')->default('viewer');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['approved', 'role']);
        });
    }
};
