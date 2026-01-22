<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Team;

class CustomerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isGod = $user->role_id === 1;
        
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
                    'team_id' => $customer->team_id,
                    'created_at' => $customer->created_at->format('d-m-Y H:i'),
                    'team' => $isGod ? [
                        'id' => $customer->team->id,
                        'name' => $customer->team->name,
                    ] : null,
                ])
                ->withQueryString(),
            'teams' => $isGod ? Team::all() : [],
            'isGod' => $isGod,
        ]);
    }
    
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        
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
