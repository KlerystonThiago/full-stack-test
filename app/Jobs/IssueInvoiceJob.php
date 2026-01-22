<?php
// app/Jobs/IssueInvoiceJob.php (VERSÃO CORRIGIDA)

namespace App\Jobs;

use App\Models\BankBillet;
use App\Models\Invoice;
use App\Services\BankService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IssueInvoiceJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $invoiceId)
    {
        //
    }

    public function handle(BankService $bankService): void
    {
        // ✅ Busca invoice com customer SEM global scope
        $invoice = Invoice::with(['customer' => fn($q) => $q->withoutGlobalScope('team')])
            ->findOrFail($this->invoiceId);

        Log::info('🔵 IssueInvoiceJob - Dados da invoice', [
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'customer_exists' => $invoice->customer ? true : false,
            'customer_name' => $invoice->customer?->name,
        ]);

        if (!$invoice->customer) {
            Log::error('❌ Customer not found for invoice', [
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
            ]);

            throw new \Exception("Customer not found for invoice {$invoice->id}");
        }

        DB::transaction(function () use ($invoice, $bankService) {

            Log::info('🟢 Calling BankService', [
                'invoice_id' => $invoice->id,
                'customer' => [
                    'id' => $invoice->customer->id,
                    'name' => $invoice->customer->name,
                    'document' => $invoice->customer->document,
                ],
            ]);

            $billetData = $bankService->generateBillet(
                $invoice->id,
                $invoice->customer,
                $invoice->amount,
                $invoice->due_date
            );

            Log::info('🟡 BankService retornou', [
                'invoice_id' => $invoice->id,
                'code_exists' => isset($billetData['code']),
                'code' => $billetData['code'] ?? 'NULL',
                'billet_data' => $billetData,
            ]);

            if (empty($billetData['code'])) {
                Log::error('❌ Bank did not return code', [
                    'invoice_id' => $invoice->id,
                    'billet_data' => $billetData,
                ]);

                throw new \Exception('Bank service did not return a billet code');
            }

            $bankBillet = BankBillet::create([
                'invoice_id' => $invoice->id,
                'code' => $billetData['code'],
                'bank_response' => $billetData['bank_response'],
                'expires_at' => $billetData['expires_at'] ?? $invoice->due_date,
            ]);

            Log::info('✅ Bank billet created successfully', [
                'invoice_id' => $invoice->id,
                'billet_id' => $bankBillet->id,
                'code' => $bankBillet->code,
            ]);

            FetchBankBilletBarcode::dispatch($bankBillet)->delay(now()->addSeconds(5));

            return $bankBillet;
        });
    }
}
