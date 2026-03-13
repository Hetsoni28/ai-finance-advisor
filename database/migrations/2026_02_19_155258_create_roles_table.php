<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {

            $table->id();

            // Role name (Admin, Manager, User)
            $table->string('name')->unique();

            // URL-safe version (admin, manager, user)
            $table->string('slug')->unique();

            // Optional description
            $table->text('description')->nullable();

            // Active / disabled role
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexing for performance
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}