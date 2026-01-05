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
        Schema::create('cms_updates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('version', 50);
            $table->string('previous_version', 50)->nullable();
            $table->enum('update_type', ['manual', 'automatic'])->default('manual');
            $table->enum('status', ['pending', 'completed', 'failed', 'rolled_back'])->default('pending');
            $table->string('backup_path')->nullable();
            $table->text('changelog')->nullable();
            $table->uuid('updated_by');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('status');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_updates');
    }
};
