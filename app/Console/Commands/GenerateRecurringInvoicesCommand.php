<?php

namespace App\Console\Commands;

use App\Enums\RecurringInvoiceStatus;
use App\Models\RecurringInvoice;
use App\Services\InvoiceService;
use App\Services\RecurringInvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateRecurringInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-recurring {--date= : The date to use for processing (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices from active recurring invoices where next_run_date equals today';

    public function __construct(
        private RecurringInvoiceService $recurringInvoiceService,
        private InvoiceService $invoiceService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting recurring invoice generation...');

        $today = $this->option('date') ? Carbon::parse($this->option('date')) : today();
        $recurringInvoices = RecurringInvoice::where('status', RecurringInvoiceStatus::ACTIVE->value)
            ->whereDate('next_run_date', $today)
            ->with(['customer', 'vendor', 'invoices', 'invoiceItems'])
            ->get();

        if ($recurringInvoices->isEmpty()) {
            $this->info('No recurring invoices found that need generation today.');

            return Command::SUCCESS;
        }

        $this->info("Found {$recurringInvoices->count()} recurring invoice(s) to process.");

        $generatedCount = 0;
        $errorCount = 0;

        foreach ($recurringInvoices as $recurringInvoice) {
            try {
                if (! $this->recurringInvoiceService->shouldGenerateInvoice($recurringInvoice)) {
                    $this->warn("Skipping recurring invoice {$recurringInvoice->number} - conditions not met.");

                    continue;
                }

                $this->info("Processing recurring invoice: {$recurringInvoice->number}");

                // Generate invoice
                $invoiceDate = Carbon::parse($recurringInvoice->next_run_date);
                $sendAutomatically = $recurringInvoice->send_automatically ?? false;

                $invoice = $this->recurringInvoiceService->generateInvoiceFromRecurring(
                    $recurringInvoice,
                    $invoiceDate,
                    $sendAutomatically
                );

                $this->info("Generated invoice: {$invoice->number}");

                if ($sendAutomatically) {
                    $result = $this->invoiceService->sendInvoiceToCustomer($invoice);
                    if ($result === true) {
                        $this->info("Sent invoice email to customer: {$invoice->customer->email}");
                    } else {
                        $this->error("Failed to send invoice email to customer: {$result}");
                        Log::error('Failed to send invoice email', [
                            'invoice_id' => $invoice->id,
                            'error' => $result instanceof \Exception ? $result->getMessage() : $result,
                        ]);
                    }
                } else {
                    $result = $this->invoiceService->notifyAdminDraftReady($invoice, $recurringInvoice);
                    if ($result === true) {
                        $this->info('Sent draft notification to admin(s)');
                    } else {
                        $this->error("Failed to send draft notification to admin: {$result}");
                        Log::error('Failed to send draft notification', [
                            'invoice_id' => $invoice->id,
                            'error' => $result instanceof \Exception ? $result->getMessage() : $result,
                        ]);
                    }
                }

                // Calculate and update next_run_date
                $nextRunDate = $this->recurringInvoiceService->calculateNextRunDate($recurringInvoice, $invoiceDate);

                // Update recurring invoice
                $updateData = [
                    'last_run_date' => $today,
                ];

                if ($nextRunDate) {
                    $updateData['next_run_date'] = $nextRunDate->toDateString();
                } else {
                    // End condition will be reached, set next_run_date to null
                    $updateData['next_run_date'] = null;
                }

                $this->recurringInvoiceService->updateRecurringInvoice($recurringInvoice->id, $updateData);

                $endReached = $this->recurringInvoiceService->checkAndUpdateEndCondition($recurringInvoice->fresh());

                if ($endReached) {
                    $this->info("Recurring invoice {$recurringInvoice->number} has reached its end condition and is now ENDED.");
                }

                $generatedCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Error processing recurring invoice {$recurringInvoice->number}: {$e->getMessage()}");
                Log::error('Error generating invoice from recurring invoice', [
                    'recurring_invoice_id' => $recurringInvoice->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info("Completed. Generated: {$generatedCount}, Errors: {$errorCount}");

        return Command::SUCCESS;
    }
}
