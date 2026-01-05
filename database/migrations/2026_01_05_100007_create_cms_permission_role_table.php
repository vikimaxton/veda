<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cms_permission_role', function (Blueprint $table) {
            $table->uuid('permission_id');
            $table->uuid('role_id');
            
            $table->foreign('permission_id')->references('id')->on('cms_permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('cms_roles')->onDelete('cascade');
            
            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_permission_role');
    }
};
