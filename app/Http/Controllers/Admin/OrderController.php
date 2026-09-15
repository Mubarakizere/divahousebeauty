<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\User;

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
                          ->orWhere('payment_token', 'LIKE', "%{$search}%")
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
        $order = Order::with(['items.product', 'user.addresses'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status (e.g. pending_payment -> paid -> shipped -> cancelled ...)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->input('status');

        // Update order status
        $order->status = $newStatus;
        $order->save();

        // Create status history record
        $order->statusHistories()->create([
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'notes' => $request->input('notes'),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Order status updated successfully.');
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
     * Update shipping cost for an order (admin enters DHL base cost).
     * Customer will pay base + 5% service fee.
     */
    public function updateShippingCost(Request $request, $id)
    {
        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'shipping_cost' => $request->input('shipping_cost'),
            'shipping_notes' => $request->input('shipping_notes'),
        ]);

        // Log in status history
        $order->statusHistories()->create([
            'status_from' => $order->status,
            'status_to' => $order->status,
            'notes' => 'Shipping cost set to RWF ' . number_format($request->input('shipping_cost'), 0) . ' (customer pays RWF ' . number_format($order->shipping_total, 0) . ' incl. 5% fee)',
            'updated_by' => auth()->id(),
        ]);

        // Send email to customer
        try {
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new \App\Mail\ShippingCostSet($order));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send shipping cost email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Shipping cost updated. Customer will pay RWF ' . number_format($order->shipping_total, 0) . ' (incl. 5% service fee). Email sent to customer.');
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
