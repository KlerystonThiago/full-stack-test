<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isGod = $user->role_id === 1;

        return inertia('customers/Index', [
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
                    'team' => $isGod ? [
                        'id' => $customer->team->id,
                        'name' => $customer->team->name,
                    ] : null,
                ])
                ->withQueryString(),
            'teams' => $isGod ? Team::select('id', 'name')->orderBy('name')->get() : [],
            'isGod' => $isGod,
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        
        $data['team_id'] = $user->current_team_id;
        
        Customer::withoutGlobalScope('team')->create($data);

        return redirect()->back()->with('success', 'Cliente criado com sucesso');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        Customer::withoutGlobalScope('team')->where('id', $customer->id)->update($data);

        return redirect()->back()->with('success', 'Cliente atualizado com sucesso');
    }

    
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->back()
            ->with('success', 'Usuário removido com sucesso.');
    }
}












































































    /* public function index()
    {
        $customers = Customer::withCount('invoices')
            ->latest()
            ->paginate(15);

        return Inertia::render('customers/Index', [
            'customers' => $customers,
        ]);
    }
    
    public function create()
    {
        return Inertia::render('customers/Create');
    }
    
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }
    
    public function show(Customer $customer)
    {
        $customer->load(['invoices' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return Inertia::render('customers/Show', [
            'customer' => $customer,
        ]);
    }
    
    public function edit(Customer $customer)
    {
        return Inertia::render('customers/Edit', [
            'customer' => $customer,
        ]);
    }
    
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }
    
    public function destroy(Customer $customer)
    {
        // Check if customer has invoices
        if ($customer->invoices()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer with existing invoices.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    } */
