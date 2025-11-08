<?php

use App\Enums\RecurringInvoiceStatus;
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
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(RecurringInvoiceStatus::DRAFT);

            $table->string('number')->unique()->nullable();
            $table->unsignedInteger('vendor_invoice_number')->nullable();

            $table->date('start_date');
            $table->unsignedInteger('due_after_days')->default(30);
            $table->string('frequency');
            $table->string('end_condition_type');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('end_count')->nullable();
            $table->boolean('send_automatically')->default(false);
            $table->date('next_run_date')->nullable();
            $table->date('last_run_date')->nullable();

            $table->longText('notes')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->default(0);

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
        Schema::dropIfExists('recurring_invoices');
    }
};
