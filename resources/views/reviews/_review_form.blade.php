{{-- Review Submission Form --}}
@auth
<div class="bg-white shadow-lg border border-slate-100 rounded-xl p-6 md:p-8 mb-8"
     x-data="{
       rating: {{ $existingReview->rating ?? 0 }},
       title: '{{ $existingReview->title ?? '' }}',
       review: '{{ $existingReview->review ?? '' }}',
       isSubmitting: false,
       isEditing: {{ isset($existingReview) ? 'true' : 'false' }},
       
       setRating(r) {
         this.rating = r;
       },
       
       showToast(message, type = 'success') {
         const toast = document.createElement('div');
         toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
           type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
         } transition-opacity duration-300 animate-fade-in`;
         toast.textContent = message;
         document.body.appendChild(toast);
         setTimeout(() => {
           toast.style.opacity = '0';
           setTimeout(() => toast.remove(), 300);
         }, 3000);
       },
       
       async submitReview() {
         if (this.rating === 0) {
           this.showToast('Please select a star rating', 'error');
           return;
         }
         
         this.isSubmitting = true;
         
         const url = this.isEditing 
           ? '{{ isset($existingReview) ? route('reviews.update', $existingReview->id) : '' }}'
           : '{{ route('reviews.store', $product->id) }}';
         
         const method = this.isEditing ? 'PUT' : 'POST';
         
         try {
           const response = await fetch(url, {
             method: method,
             headers: {
               'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
               'Accept': 'application/json',
               'Content-Type': 'application/json'
             },
             body: JSON.stringify({
               rating: this.rating,
               title: this.title,
               review: this.review
             })
           });
           
           const data = await response.json();
           
           if (data.success) {
             this.showToast(data.message, 'success');
             setTimeout(() => window.location.reload(), 1500);
           } else {
             this.showToast(data.message || 'Failed to submit review', 'error');
           }
         } catch (error) {
           console.error('Error:', error);
           this.showToast('An error occurred. Please try again.', 'error');
         } finally {
           this.isSubmitting = false;
         }
       }
     }">
  
  <h3 class="text-xl font-serif text-[var(--black)] mb-6 pb-3 border-b border-slate-200">
    <span x-show="!isEditing">Write a Review</span>
    <span x-show="isEditing" x-cloak>Edit Your Review</span>
  </h3>
  
  <!-- Star Rating Selector -->
  <div class="mb-6">
    <label class="block text-sm font-semibold text-slate-700 mb-3">Your Rating *</label>
    <div class="flex items-center gap-1">
      <template x-for="i in 5" :key="i">
        <button type="button"
                @click="setRating(i)"
                class="text-3xl transition-all duration-200 transform hover:scale-110"
                :class="i <= rating ? 'text-[var(--gold)]' : 'text-slate-300 hover:text-yellow-300'">
          <i class="la la-star"></i>
        </button>
      </template>
      <span x-show="rating > 0" class="ml-3 text-sm font-medium text-slate-700" x-text="rating + ' star' + (rating > 1 ? 's' : '')"></span>
    </div>
  </div>
  
  <!-- Title -->
  <div class="mb-6">
    <label class="block text-sm font-semibold text-slate-700 mb-3">Review Title <span class="text-slate-400 font-normal">(Optional)</span></label>
    <input type="text"
           x-model="title"
           maxlength="100"
           placeholder="Summarize your experience..."
           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--gold)] focus:border-transparent transition-all">
  </div>
  
  <!-- Review Text -->
  <div class="mb-6">
    <label class="block text-sm font-semibold text-slate-700 mb-3">Your Review <span class="text-slate-400 font-normal">(Optional)</span></label>
    <textarea x-model="review"
              rows="5"
              maxlength="1000"
              placeholder="Share your thoughts about this product..."
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--gold)] focus:border-transparent transition-all resize-none"></textarea>
    <div class="mt-2 text-xs text-slate-500">
      <span x-text="review.length"></span> / 1000 characters
    </div>
  </div>
  
  <!-- Submit Button -->
  <button type="button"
          @click="submitReview()"
          :disabled="isSubmitting || rating === 0"
          class="inline-flex items-center gap-2 px-8 py-3 bg-[var(--black)] text-white rounded-lg font-semibold hover:bg-[var(--gold)] disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 shadow-md hover:shadow-lg">
    <i class="la la-paper-plane"></i>
    <span x-show="!isSubmitting">
      <span x-show="!isEditing">Submit Review</span>
      <span x-show="isEditing" x-cloak>Update Review</span>
    </span>
    <span x-show="isSubmitting" x-cloak>Submitting...</span>
  </button>
</div>
@endauth

@guest
<div class="bg-gradient-to-br from-slate-50 to-gray-50 border border-slate-200 rounded-xl p-8 text-center shadow-sm">
  <i class="la la-user-circle text-5xl text-slate-400 mb-3"></i>
  <p class="text-slate-700 font-semibold mb-4">Please log in to write a review</p>
  <button type="button"
          @click="window.dispatchEvent(new CustomEvent('open-auth', { detail: { tab: 'signin' } }))"
          class="inline-flex items-center gap-2 px-6 py-3 bg-[var(--black)] text-white rounded-lg font-semibold hover:bg-[var(--gold)] transition-all duration-300">
    <i class="la la-sign-in-alt"></i>
    Log In
  </button>
</div>
@endguest
