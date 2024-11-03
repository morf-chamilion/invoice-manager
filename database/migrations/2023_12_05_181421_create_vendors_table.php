<?php

use App\Enums\VendorStatus;
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
		Schema::create('vendors', function (Blueprint $table) {
			$table->id();
			$table->tinyInteger('status')->default(VendorStatus::INACTIVE);

			$table->string('name')->unique();
			$table->string('currency');
			$table->string('reference_number_prefix');

			$table->string('address')->nullable();
			$table->string('bank_account_details')->nullable();
			$table->string('quotation_footer_content')->nullable();
			$table->string('invoice_footer_content')->nullable();

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
		Schema::dropIfExists('vendors');
	}
};
