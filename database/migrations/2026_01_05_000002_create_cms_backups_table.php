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
        Schema::create('cms_backups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('version', 50);
            $table->string('backup_path');
            $table->bigInteger('file_size')->unsigned();
            $table->uuid('created_by');
            $table->timestamp('created_at');
            $table->timestamp('restored_at')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('version');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_backups');
    }
};
