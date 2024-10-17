<?php

use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\Vendor;
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
		Schema::create('quotations', function (Blueprint $table) {
			$table->id();
			$table->tinyInteger('status')->default(QuotationStatus::DRAFT);

			$table->string('number')->unique()->nullable();
			$table->unsignedInteger('vendor_quotation_number')->nullable();

			$table->date('date');
			$table->date('due_date');

			$table->longText('notes')->nullable();
			$table->decimal('total_price', 10, 2)->default(0);

			$table->foreignIdFor(Customer::class)->constrained();
			$table->foreignIdFor(Vendor::class)->constrained()->cascadeOnDelete();

			$table->foreignId('created_by')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('cascade');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('quotations');
	}
};
