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
        await this.fetchRates();
        this.attachEventListeners();
        this.updateAllPrices();
        this.updateSelectorUI();
        this.initialized = true;
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
            // Hardcoded fallback rates
            this.rates = { USD: 0.00076, EUR: 0.00071, GBP: 0.00061, RWF: 1 };
            this.currencies = {
                USD: { code: 'USD', symbol: '$', name: 'US Dollar', flag: '🇺🇸', decimals: 2 },
                EUR: { code: 'EUR', symbol: '€', name: 'Euro', flag: '🇪🇺', decimals: 2 },
                GBP: { code: 'GBP', symbol: '£', name: 'British Pound', flag: '🇬🇧', decimals: 2 },
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
        
        priceElements.forEach(element => {
            const rwfAmount = parseFloat(element.getAttribute('data-price-rwf'));
            
            if (!isNaN(rwfAmount)) {
                // Add fade-out animation
                element.style.opacity = '0.5';
                
                setTimeout(() => {
                    const converted = this.convert(rwfAmount);
                    const formatted = this.formatPrice(converted);
                    
                    // Update the price text
                    if (element.classList.contains('convertible-price')) {
                        element.textContent = formatted;
                    } else {
                        element.innerHTML = formatted;
                    }
                    
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
        // Currency selector dropdown
        const currencyItems = document.querySelectorAll('.currency-item');
        
        currencyItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedCurrency = item.getAttribute('data-currency');
                this.changeCurrency(selectedCurrency);
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.currency-selector')) {
                this.closeDropdown();
            }
        });
    }

    /**
     * Change active currency
     */
    changeCurrency(currency) {
        this.saveCurrency(currency);
        this.updateAllPrices();
        this.updateSelectorUI();
        this.closeDropdown();
    }

    /**
     * Update currency selector UI
     */
    updateSelectorUI() {
        const selectedDisplay = document.querySelector('.currency-selected');
        const currencyItems = document.querySelectorAll('.currency-item');
        
        if (selectedDisplay && this.currencies[this.currentCurrency]) {
            const current = this.currencies[this.currentCurrency];
            selectedDisplay.innerHTML = `
                <span class="currency-flag">${current.flag}</span>
                <span class="currency-code">${current.code}</span>
            `;
        }

        // Update active state
        currencyItems.forEach(item => {
            if (item.getAttribute('data-currency') === this.currentCurrency) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    /**
     * Close dropdown
     */
    closeDropdown() {
        const dropdown = document.querySelector('.currency-dropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
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

// Toggle dropdown function (called from HTML)
function toggleCurrencyDropdown() {
    const dropdown = document.querySelector('.currency-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}
