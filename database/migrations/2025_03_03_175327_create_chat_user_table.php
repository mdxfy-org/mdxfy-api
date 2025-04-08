<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat.chat_user', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('chat_id');
            $table->foreignId('user_id')->constrained('hr.user')->onDelete('cascade');
            $table->timestamp('joined_in')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('left_in')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('inactivated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat.chat_user');
    }
};
