<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return inertia('admin/customers/Index', [
            'customers' => Customer::query()
                ->withCount('invoices')
                ->orderByDesc('id')
                ->paginate(6)
                ->onEachSide(2)
                ->through(fn ($customer) => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'document' => $customer->document,
                    'address' => $customer->address,
                    'invoices_count' => $customer->invoices_count,
                    'created_at' => $customer->created_at->format('d-m-Y H:i'),
                ])
                
                ->withQueryString()
        ]);
    }
    
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        Customer::create($data);

        return redirect()->back()->with('success', 'Usuário criado com sucesso');
    }
    
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'document' => $data['document'],
            'address' => $data['address'],
        ]); 

        return redirect()->back();
    }
    
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->back()
            ->with('success', 'Usuário removido com sucesso.');
    }
}
