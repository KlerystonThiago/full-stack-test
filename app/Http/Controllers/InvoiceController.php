<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Status;
use App\Models\Product;
use App\Actions\IssueInvoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Jobs\IssueInvoiceJob;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function index()
    {
        /* $invoice = Invoice::find(25);
        dd($invoice->bankBillet); */
        return inertia('invoices/Index', [
            'invoices' => Invoice::query()
                ->with(['customer' => fn($q) => $q->withoutGlobalScope('team')])
                ->with('customer')
                ->orderByDesc('id')
                ->paginate(5)
                ->onEachSide(2)
                ->through(fn ($invoice) => [
                    'id' => $invoice->id,
                    'code' => $invoice->code,
                    'customer_id' => $invoice->customer_id,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date->format('d-m-Y'),
                    'due_date' => $invoice->due_date->format('d-m-Y'),
                    'customer' => $invoice->customer,
                    'products' => $invoice->products,
                    'bankBillet' => $invoice->bankBillet,
                ])
            ->withQueryString(),
            'customers' => Customer::select('id', 'name')->get(),
            'status' => Status::select('id', 'name')->get(),
            'products' => Product::all(),
        ]);
    }

    public function store(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $lastInvoice = Invoice::whereYear('created_at', date('Y'))
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastInvoice ?
                (int) substr($lastInvoice->code, -5) + 1 :
                1;

            $code = 'INV-' . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'code' => $code,
                'customer_id' => $validated['customer_id'],
                'status_id' => $validated['status_id'],
                'amount' => $validated['amount'],
                'issue_date' => now(),
                'due_date' => $validated['due_date'],
                'team_id' => auth()->user()->current_team_id ?? null,
            ]);

            foreach ($validated['products'] as $product) {
                $productModel = Product::findOrFail($product['product_id']);
                $subtotal = $product['price'] * $product['quantity'];

                $invoice->products()->attach($product['product_id'], [
                    'name' => $productModel->name,
                    'description' => $productModel->description,
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['price'],
                    'amount' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            if ($invoice->status_id == 2) {
                IssueInvoiceJob::dispatch($invoice->id);
            }

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Fatura criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar fatura. Tente novamente.');
        }
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $user = auth()->user();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $invoice->load('bankBillet');
            $hasBillet = $invoice->bankBillet !== null;

            $updateData = [
                'customer_id' => $validated['customer_id'],
                'status_id' => $validated['status_id'],
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
            ];

            if ($user->role_id === 1 && isset($validated['team_id'])) {
                $updateData['team_id'] = $validated['team_id'];
            }

            $invoice->fill($updateData)->save();

            $invoice->products()->detach();

            foreach ($validated['products'] as $product) {
                $productModel = Product::find($product['product_id']);
                $subtotal = $product['price'] * $product['quantity'];

                $invoice->products()->attach($product['product_id'], [
                    'name' => $productModel->name,
                    'description' => $productModel->description,
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['price'],
                    'amount' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $isPending = $invoice->status_id == 2;

            if ($isPending && $hasBillet) {
                $billet = $invoice->bankBillet;
                $bankResponse = $billet->bank_response;

                $bankResponse['amount'] = $validated['amount'];
                $bankResponse['due_date'] = \Carbon\Carbon::parse($validated['due_date'])->format('Y-m-d');
                $bankResponse['expires_at'] = \Carbon\Carbon::parse($validated['due_date'])->endOfDay()->toIso8601String();

                if ($invoice->customer) {
                    $bankResponse['name'] = $invoice->customer->name;
                    $bankResponse['document'] = $invoice->customer->document ?? 'N/A';
                }

                $billet->update([
                    'bank_response' => $bankResponse,
                    'expires_at' => $validated['due_date'],
                ]);
            } elseif ($isPending && !$hasBillet) {
                IssueInvoiceJob::dispatch($invoice->id);
            }

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Fatura atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar fatura: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Fatura deletada com sucesso!');
    }

    public function restore($id)
    {
        $invoice = Invoice::withTrashed()->findOrFail($id);
        $invoice->restore();

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Fatura restaurada com sucesso!');
    }
}
