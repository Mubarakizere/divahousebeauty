<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Show list of orders with filters/search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Order::query()
            ->with('user') // so we can show who created it, if needed
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('customer_name', 'LIKE', "%{$search}%")
                          ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                          ->orWhere('customer_email', 'LIKE', "%{$search}%")
                          ->orWhere('masked_order_id', 'LIKE', "%{$search}%")
                          ->orWhere('transaction_id', 'LIKE', "%{$search}%")
                          ->orWhere('id', $search); // allow searching by raw ID
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc');

        $orders = $query->paginate(15)->withQueryString();

        // for nice row numbering in Blade
        $rowStart = ($orders->currentPage() - 1) * $orders->perPage();

        return view('admin.orders.index', compact('orders', 'rowStart', 'search', 'status'));
    }

    /**
     * Show single order details, items, status controls, etc.
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status (e.g. pending_payment -> paid -> shipped -> cancelled ...)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Order status updated.');
    }

    /**
     * Mark order as paid (set is_paid=1, paid_at=now(), status='paid' if you want).
     */
    public function markPaid($id)
    {
        $order = Order::findOrFail($id);

        $order->is_paid = true;
        $order->paid_at = now();

        // OPTIONAL: if you want status to also flip to "paid"
        if ($order->status === 'pending_payment') {
            $order->status = 'paid';
        }

        $order->save();

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Order marked as paid.');
    }

    /**
     * Delete an order completely (danger).
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete(); // to avoid FK issues then...
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
