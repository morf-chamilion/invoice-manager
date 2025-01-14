<?php

use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
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
		Schema::create('invoices', function (Blueprint $table) {
			$table->id();
			$table->tinyInteger('status')->default(InvoiceStatus::DRAFT);

			$table->string('number')->unique()->nullable();
			$table->unsignedInteger('vendor_invoice_number')->nullable();

			$table->date('date');
			$table->date('due_date');

			$table->longText('notes')->nullable();
			$table->decimal('total_price', 10, 2)->default(0);

			$table->tinyInteger('payment_status')->default(InvoicePaymentStatus::PENDING);

			$table->foreignIdFor(Vendor::class)->constrained()->cascadeOnDelete();
			$table->foreignIdFor(Customer::class)->constrained();

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
		Schema::dropIfExists('invoices');
	}
};
