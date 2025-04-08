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
        Schema::create('integration.application', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->string('client_id')->unique();
            $table->string('client_secret');
            $table->string('redirect_uri');
            $table->json('scopes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('inactivated_at')->nullable();
        });

        Schema::create('integration.user_connections', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('user_id')->constrained('hr.user')->onDelete('cascade');
            $table->foreignId('application_id')->constrained('integration.application')->onDelete('cascade');
            $table->string('provider');
            $table->string('provider_id')->unique();
            $table->json('provider_meta')->nullable();
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
        Schema::dropIfExists('integration.user_connections');
        Schema::dropIfExists('integration.application');
    }
};
