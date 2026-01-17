<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Status;
use App\Models\Product;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;

class InvoiceController extends Controller
{
    public function index()
    {
        return inertia('admin/invoices/Index', [
            'invoices' => Invoice::query()
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
            return redirect()
                ->route('admin.invoices.index')
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
        
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();

            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'status_id' => $validated['status_id'],
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
            ]);
            
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

            return redirect()
                ->route('admin.invoices.index')
                ->with('success', 'Fatura atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar invoice: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro: ' . $e->getMessage());
        }
    }
    
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('admin.invoices.index')
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
