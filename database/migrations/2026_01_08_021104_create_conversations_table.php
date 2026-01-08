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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('project'); // 'project' or 'private'
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user1_id')->nullable()->constrained('users')->onDelete('cascade'); // For private chat
            $table->foreignId('user2_id')->nullable()->constrained('users')->onDelete('cascade'); // For private chat
            $table->string('title')->nullable(); // Project name for project chat, or custom for private
            $table->boolean('is_closed')->default(false); // Auto-close when project ends
            $table->timestamp('last_message_at')->nullable(); // Last message timestamp
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'project_id']);
            $table->index(['type', 'user1_id', 'user2_id']);
            $table->index('is_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
