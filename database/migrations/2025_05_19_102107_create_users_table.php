<?php
declare(strict_types=1);
use Core\Schema\Schema;

return new class {
    public function up(): void
    {
        Schema::instance()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::instance()->dropIfExists('users');
    }
};