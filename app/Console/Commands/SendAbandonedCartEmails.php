<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Mail\AbandonedCartEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cart:send-abandonment-emails';

    /**
     * The console command description.
     */
    protected $description = 'Send reminder emails for abandoned carts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding abandoned carts...');

        // Find carts abandoned 1-24 hours ago, not yet reminded
        $carts = AbandonedCart::where('abandoned_at', '>=', now()->subHours(24))
            ->where('abandoned_at', '<=', now()->subHours(1))
            ->whereNull('reminder_sent_at')
            ->where('is_recovered', false)
            ->get();

        $this->info("Found {$carts->count()} abandoned carts");

        foreach ($carts as $cart) {
            try {
                Mail::to($cart->email)->send(new AbandonedCartEmail($cart));
                
                $cart->update(['reminder_sent_at' => now()]);
                
                $this->info("✓ Sent reminder to {$cart->email}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$cart->email}: " . $e->getMessage());
            }
        }

        $this->info('Done!');
        
        return 0;
    }
}
