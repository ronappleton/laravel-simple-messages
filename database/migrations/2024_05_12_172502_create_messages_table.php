<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('sender');
            $table->morphs('recipient');
            $table->timestamp('sent_at')->nullable()->useCurrent();
            $table->timestamp('read_at')->nullable();
            $table->json('notifications')->nullable();
            $table->text('content');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
