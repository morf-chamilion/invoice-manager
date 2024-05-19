<?php

use App\Enums\PageStatus;
use App\Providers\PageServiceProvider;
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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(PageStatus::INACTIVE);

            $table->string('slug')->unique();
            $table->string('title');
            $table->string('admin_template')->default(PageServiceProvider::TEMPLATE_DEFAULT_ADMIN_VIEW);
            $table->string('front_template')->default(PageServiceProvider::TEMPLATE_DEFAULT_FRONT_VIEW);

            $table->foreignId('created_by')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();

            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
