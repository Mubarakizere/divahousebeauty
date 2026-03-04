@push('head')
    <style>
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slideInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        .glass-form-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        
        .form-label {
            @apply block text-xs font-black text-slate-500 uppercase tracking-widest mb-2;
        }
        
        .premium-input {
            @apply w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-purple-100 focus:border-purple-400 transition-all duration-300 text-slate-900 font-medium;
        }
    </style>
@endpush

<div class="max-w-4xl mx-auto animate-slide-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                @if(isset($coupon))
                    <i class="fas fa-magic text-indigo-600 mr-2"></i>
                    Refine Coupon
                @else
                    <i class="fas fa-sparkles text-purple-600 mr-2"></i>
                    Craft New Discount
                @endif
            </h1>
            <p class="mt-1 text-slate-500 font-medium italic">
                @if(isset($coupon))
                    Adjusting parameters for "{{ $coupon->code }}"
                @else
                    Define rewards to delight your customers
                @endif
            </p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" 
           class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2 text-xs"></i>
            Back to List
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-400 mr-3 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="glass-form-card rounded-3xl overflow-hidden shadow-2xl shadow-purple-900/5">
        <form action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" 
              method="POST" 
              class="p-8 md:p-10 space-y-8"
              x-data="{
                  type: '{{ old('type', $coupon->type ?? 'percentage') }}',
                  isActive: {{ old('is_active', $coupon->is_active ?? true) ? 'true' : 'false' }}
              }">
            @csrf
            @if(isset($coupon)) @method('PUT') @endif

            <!-- Section: Identity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="code" class="form-label">Coupon Code *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-tag text-slate-400"></i>
                        </div>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $coupon->code ?? '') }}"
                               required
                               placeholder="e.g., DIVA2024"
                               class="premium-input pl-11 uppercase tracking-widest font-mono">
                    </div>
                    <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase italic">Automatic uppercase enabled</p>
                </div>

                <div>
                    <label for="type" class="form-label">Reward Strategy *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-gift text-slate-400"></i>
                        </div>
                        <select id="type" 
                                name="type" 
                                x-model="type"
                                required
                                class="premium-input pl-11 appearance-none">
                            <option value="percentage">Percentage Reduction (%)</option>
                            <option value="fixed">Fixed Cash Discount (RWF)</option>
                            <option value="free_shipping">Complimentary Shipping</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Values -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                <div x-show="type !== 'free_shipping'" x-transition>
                    <label for="value" class="form-label">Discount Magnitude *</label>
                    <div class="relative">
                        <input type="number" 
                               id="value" 
                               name="value" 
                               value="{{ old('value', $coupon->value ?? '') }}"
                               step="0.01"
                               min="0"
                               :required="type !== 'free_shipping'"
                               class="premium-input text-lg font-black text-purple-700">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <span x-text="type === 'percentage' ? '%' : 'RWF'" class="font-black text-slate-400"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="min_order_amount" class="form-label">Threshold Requirement</label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-xs font-bold text-slate-400">RWF</span>
                        </div>
                        <input type="number" 
                               id="min_order_amount" 
                               name="min_order_amount" 
                               value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="premium-input pl-14">
                    </div>
                    <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase italic">Minimum cart value required</p>
                </div>
            </div>

            <!-- Section: Limits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div x-show="type === 'percentage'" x-transition>
                    <label for="max_discount" class="form-label">Max Cap (RWF)</label>
                    <input type="number" 
                           id="max_discount" 
                           name="max_discount" 
                           value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
                           step="0.01"
                           min="0"
                           placeholder="Unlimited"
                           class="premium-input">
                </div>

                <div>
                    <label for="usage_limit" class="form-label">Campaign Budget</label>
                    <input type="number" 
                           id="usage_limit" 
                           name="usage_limit" 
                           value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                           min="1"
                           placeholder="Unlimited Uses"
                           class="premium-input">
                </div>

                <div>
                    <label for="usage_limit_per_user" class="form-label">Per Customer Cap</label>
                    <input type="number" 
                           id="usage_limit_per_user" 
                           name="usage_limit_per_user" 
                           value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? 1) }}"
                           min="1"
                           required
                           class="premium-input">
                </div>
            </div>

            <!-- Section: Timeline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-indigo-50/30 p-6 rounded-2xl border border-indigo-50">
                <div>
                    <label for="starts_at" class="form-label">Launch Sequence</label>
                    <input type="datetime-local" 
                           id="starts_at" 
                           name="starts_at" 
                           value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                           class="premium-input">
                </div>

                <div>
                    <label for="expires_at" class="form-label">Termination Date</label>
                    <input type="datetime-local" 
                           id="expires_at" 
                           name="expires_at" 
                           value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                           class="premium-input">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="form-label">Internal Briefing</label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          maxlength="500"
                          placeholder="Optional notes for the admin team..."
                          class="premium-input resize-none">{{ old('description', $coupon->description ?? '') }}</textarea>
            </div>

            <!-- Active Status Switch -->
            <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-2xl border border-emerald-100/50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-emerald-600 shadow-sm">
                        <i class="fas fa-toggle-on" x-show="isActive"></i>
                        <i class="fas fa-toggle-off text-slate-300" x-show="!isActive"></i>
                    </div>
                    <div>
                        <p class="text-sm font-black text-emerald-900 leading-none">Campaign Visibility</p>
                        <p class="text-[10px] font-bold text-emerald-700/60 uppercase mt-1">Determine if customers can redeem this code</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row justify-end gap-4 pt-10">
                <a href="{{ route('admin.coupons.index') }}" 
                   class="px-8 py-4 text-center font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest text-[11px]">
                    Abandon Changes
                </a>
                <button type="submit" 
                        class="px-10 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-purple-200 hover:shadow-purple-300 hover:-translate-y-1 transition-all active:scale-95 uppercase tracking-widest text-[11px]">
                    @if(isset($coupon))
                        Authorize Update
                    @else
                        Initialize Campaign
                    @endif
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-2">Coupon Tips:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Percentage:</strong> e.g., 20% off the order total</li>
                    <li><strong>Fixed Amount:</strong> e.g., RWF 5000 off the order</li>
                    <li><strong>Free Shipping:</strong> Waives shipping fees</li>
                    <li>Set usage limits to control budget</li>
                    <li>Use start/end dates for time-limited promotions</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
