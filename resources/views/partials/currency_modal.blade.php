<div x-data="{
    open: false,
    init() {
        if (!localStorage.getItem('selected_currency')) {
            setTimeout(() => this.open = true, 1000);
        }
    },
    selectCurrency(code) {
        if (window.currencyConverter) {
            window.currencyConverter.changeCurrency(code);
            this.open = false;
        }
    }
}"
x-show="open"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
style="display: none;">

    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden" 
         @click.outside="open = false">
        
        {{-- Header --}}
        <div class="bg-white px-6 py-5 border-b border-gray-100 text-center">
            <h3 class="text-xl font-serif text-slate-900">Welcome to Diva House</h3>
            <p class="text-sm text-slate-500 mt-1">Please select your preferred currency</p>
        </div>

        {{-- Currency Options --}}
        <div class="p-4 grid grid-cols-2 gap-3">
            
            <button @click="selectCurrency('RWF')" 
                    class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-200 hover:border-[var(--gold)] hover:bg-yellow-50/30 transition-all group">
                <span class="text-4xl mb-2 grayscale group-hover:grayscale-0 transition-all">🇷🇼</span>
                <span class="font-bold text-slate-800">RWF</span>
                <span class="text-xs text-slate-500">Rwandan Franc</span>
            </button>

            <button @click="selectCurrency('USD')" 
                    class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-200 hover:border-[var(--gold)] hover:bg-yellow-50/30 transition-all group">
                <span class="text-4xl mb-2 grayscale group-hover:grayscale-0 transition-all">🇺🇸</span>
                <span class="font-bold text-slate-800">USD</span>
                <span class="text-xs text-slate-500">US Dollar</span>
            </button>

            <button @click="selectCurrency('KES')" 
                    class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-200 hover:border-[var(--gold)] hover:bg-yellow-50/30 transition-all group">
                <span class="text-4xl mb-2 grayscale group-hover:grayscale-0 transition-all">🇰🇪</span>
                <span class="font-bold text-slate-800">KES</span>
                <span class="text-xs text-slate-500">Kenyan Shilling</span>
            </button>

            <button @click="selectCurrency('EUR')" 
                    class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-200 hover:border-[var(--gold)] hover:bg-yellow-50/30 transition-all group">
                <span class="text-4xl mb-2 grayscale group-hover:grayscale-0 transition-all">🇪🇺</span>
                <span class="font-bold text-slate-800">EUR</span>
                <span class="text-xs text-slate-500">Euro</span>
            </button>

        </div>

    </div>
</div>
