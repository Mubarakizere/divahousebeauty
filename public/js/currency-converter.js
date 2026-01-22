/**
 * Currency Converter - Professional Multi-Currency System
 * Handles real-time currency conversion with smooth animations
 */

class CurrencyConverter {
    constructor() {
        this.baseCurrency = 'RWF';
        this.currentCurrency = this.getSavedCurrency();
        this.rates = {};
        this.currencies = {};
        this.initialized = false;

        this.init();
    }

    /**
     * Initialize the currency converter
     */
    async init() {
        console.log('Currency Converter initializing...');
        await this.fetchRates();
        console.log('Rates loaded:', this.rates);
        this.attachEventListeners();
        this.updateAllPrices();
        this.updateSelectorUI();
        this.initialized = true;
        console.log('Currency Converter initialized! Current currency:', this.currentCurrency);
    }

    /**
     * Fetch exchange rates from API
     */
    async fetchRates() {
        try {
            const response = await fetch('/api/currency/rates');
            const data = await response.json();

            if (data.success) {
                this.rates = data.rates;
                this.currencies = data.currencies;
                localStorage.setItem('currency_rates', JSON.stringify(this.rates));
                localStorage.setItem('currency_data', JSON.stringify(this.currencies));
                localStorage.setItem('rates_last_updated', Date.now());
            } else {
                // Fallback to cached rates
                this.loadCachedRates();
            }
        } catch (error) {
            console.error('Error fetching currency rates:', error);
            this.loadCachedRates();
        }
    }

    /**
     * Load cached rates from localStorage
     */
    loadCachedRates() {
        const cachedRates = localStorage.getItem('currency_rates');
        const cachedData = localStorage.getItem('currency_data');

        if (cachedRates && cachedData) {
            this.rates = JSON.parse(cachedRates);
            this.currencies = JSON.parse(cachedData);
        } else {
            // Hardcoded fallback rates (use your actual rates from API)
            this.rates = { USD: 0.0006848, EUR: 0.00058567, KES: 0.11, RWF: 1 };
            this.currencies = {
                USD: { code: 'USD', symbol: '$', name: 'US Dollar', flag: '🇺🇸', decimals: 2 },
                EUR: { code: 'EUR', symbol: '€', name: 'Euro', flag: '🇪🇺', decimals: 2 },
                KES: { code: 'KES', symbol: 'KSh', name: 'Kenyan Shilling', flag: '🇰🇪', decimals: 0 },
                RWF: { code: 'RWF', symbol: 'RWF', name: 'Rwandan Franc', flag: '🇷🇼', decimals: 0 }
            };
        }
    }

    /**
     * Get saved currency preference
     */
    getSavedCurrency() {
        return localStorage.getItem('selected_currency') || 'USD';
    }

    /**
     * Save currency preference
     */
    saveCurrency(currency) {
        localStorage.setItem('selected_currency', currency);
        this.currentCurrency = currency;
    }

    /**
     * Convert amount from RWF to target currency
     */
    convert(amount, targetCurrency = null) {
        const target = targetCurrency || this.currentCurrency;
        const rate = this.rates[target] || 1;
        return amount * rate;
    }

    /**
     * Format price with currency symbol and decimals
     */
    formatPrice(amount, currency = null) {
        const curr = currency || this.currentCurrency;
        const currencyData = this.currencies[curr] || { symbol: curr, decimals: 2 };

        const decimals = currencyData.decimals;
        const formatted = amount.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });

        if (curr === 'RWF') {
            return `RWF ${formatted}`;
        }

        return `${currencyData.symbol}${formatted}`;
    }

    /**
     * Update all convertible prices on the page
     */
    updateAllPrices() {
        const priceElements = document.querySelectorAll('.convertible-price, [data-price-rwf]');

        console.log(`Found ${priceElements.length} price elements to convert to ${this.currentCurrency}`);

        priceElements.forEach(element => {
            const rwfAmount = parseFloat(element.getAttribute('data-price-rwf'));

            if (!isNaN(rwfAmount)) {
                // Add fade-out animation
                element.style.opacity = '0.5';

                setTimeout(() => {
                    const converted = this.convert(rwfAmount);
                    const formatted = this.formatPrice(converted);

                    // Update the price text
                    element.textContent = formatted;

                    // Update currency attribute
                    element.setAttribute('data-currency', this.currentCurrency);

                    // Fade back in
                    element.style.opacity = '1';
                }, 150);
            }
        });
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Currency selector dropdown items
        const currencyItems = document.querySelectorAll('.currency-item');

        currencyItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const selectedCurrency = item.getAttribute('data-currency');
                this.changeCurrency(selectedCurrency);
            });
        });
    }

    /**
     * Change active currency
     */
    changeCurrency(currency) {
        console.log('Changing currency from', this.currentCurrency, 'to', currency);
        this.saveCurrency(currency);
        this.updateSelectorUI();
        this.updateAllPrices();
    }

    /**
     * Update currency selector UI (Tailwind version)
     */
    updateSelectorUI() {
        if (!this.currencies[this.currentCurrency]) {
            console.error('Currency data not found for:', this.currentCurrency);
            return;
        }

        const current = this.currencies[this.currentCurrency];
        console.log('Updating selector to:', current);

        // Find and update all currency selector buttons
        const buttons = document.querySelectorAll('button');

        buttons.forEach(button => {
            // Check if this is a currency button by looking for flag and code spans
            const flagSpan = button.querySelector('.text-lg.leading-none');
            const codeSpan = button.querySelector('.font-semibold');
            const svgIcon = button.querySelector('svg');

            // If it has all three elements, it's probably our currency button
            if (flagSpan && codeSpan && svgIcon) {
                flagSpan.textContent = current.flag;
                codeSpan.textContent = current.code;
            }
        });

        // Update active state on dropdown items
        const currencyItems = document.querySelectorAll('.currency-item');
        currencyItems.forEach(item => {
            const itemCurrency = item.getAttribute('data-currency');
            if (itemCurrency === this.currentCurrency) {
                item.classList.add('bg-blue-50');
                // Add checkmark if not present
                if (!item.querySelector('.checkmark-icon')) {
                    const check = document.createElement('span');
                    check.className = 'checkmark-icon ml-auto text-blue-600 font-bold';
                    check.textContent = '✓';
                    item.appendChild(check);
                }
            } else {
                item.classList.remove('bg-blue-50');
                // Remove checkmark
                const check = item.querySelector('.checkmark-icon');
                if (check) check.remove();
            }
        });

        console.log('Currency selector UI updated to:', this.currentCurrency);
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.currencyConverter = new CurrencyConverter();
    });
} else {
    window.currencyConverter = new CurrencyConverter();
}
