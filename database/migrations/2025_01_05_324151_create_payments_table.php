<?php

use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\Invoice;
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
		Schema::create('payments', function (Blueprint $table) {
			$table->id();
			$table->tinyInteger('status');

			$table->date('date');
			$table->string('number')->unique()->nullable();

			$table->foreignIdFor(Vendor::class)->constrained()->cascadeOnDelete();
			$table->foreignIdFor(Customer::class)->constrained();

			$table->foreignIdFor(Invoice::class)->nullable()->constrained()->nullOnDelete();

			$table->decimal('amount', 10, 2)->default(0);

			$table->tinyInteger('method')->default(PaymentMethod::CASH);

			$table->string('notes')->nullable();

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
		Schema::dropIfExists('payments');
	}
};
