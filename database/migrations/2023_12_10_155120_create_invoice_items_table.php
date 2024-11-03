<?php

use App\Models\Invoice;
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
		Schema::create('invoice_items', function (Blueprint $table) {
			$table->id();

			$table->foreignIdFor(Invoice::class)->constrained()->cascadeOnDelete();

			$table->string('custom')->nullable();
			$table->string('description')->nullable();

			$table->decimal('unit_price', 10, 2)->nullable();
			$table->integer('quantity')->nullable();
			$table->decimal('amount', 10, 2)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('invoice_items');
	}
};
