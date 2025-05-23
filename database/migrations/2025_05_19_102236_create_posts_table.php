<?php

declare(strict_types=1);
use Core\Schema\Schema;

return new class {
    public function up(): void
    {
        Schema::instance()->create('posts', function ($table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->boolean('published')->nullable();
            $table->foreignId('user_id');
            $table->timestamps();
            $table->index(['user_id', 'published']);
        });
    }

    public function down(): void
    {
        Schema::instance()->dropIfExists('posts');
    }
};