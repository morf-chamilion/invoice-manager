<?php

use App\Enums\InvoiceItemType;
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

			$table->tinyInteger('type')->default(InvoiceItemType::DESCRIPTION);
			$table->string('content')->nullable();
			$table->integer('quantity')->nullable();
			$table->unsignedDecimal('unit_price', 10, 2)->nullable();
			$table->unsignedDecimal('amount', 10, 2)->nullable();
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
