<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from API';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyService $currencyService): int
    {
        $this->info('Fetching latest currency exchange rates...');

        $success = $currencyService->updateCachedRates();

        if ($success) {
            $this->info('✓ Currency rates updated successfully!');
            
            // Display current rates
            $rates = $currencyService->getAllRates();
            $this->newLine();
            $this->info('Current rates (from RWF):');
            
            foreach ($rates as $currency => $rate) {
                if ($currency !== 'RWF') {
                    $this->line("  {$currency}: {$rate}");
                }
            }
            
            return Command::SUCCESS;
        } else {
            $this->error('✗ Failed to update currency rates. Check logs for details.');
            return Command::FAILURE;
        }
    }
}
