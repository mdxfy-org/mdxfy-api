<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post.post', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->text('content');
            $table->enum('visibility', ['public', 'private', 'friends'])->default('public');
            $table->foreignId('answer_to')->nullable()->constrained('chat.message')->onDelete('cascade');
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->string('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post.post');
    }
};
