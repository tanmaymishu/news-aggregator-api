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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('Uncategorized')->index();
            $table->string('source')->default('Unknown')->index();
            $table->string('title')->index();
            $table->text('description');
            $table->text('content');
            $table->text('web_url')->unique(); // text, because some sources have really long URLs
            $table->text('featured_image_url'); // text, because some sources have really long URLs
            $table->string('author')->default('Staff Reporter')->index();
            $table->dateTime('published_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
