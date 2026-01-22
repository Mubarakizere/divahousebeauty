<div id="global-loader" 
     class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center transition-opacity duration-500 ease-in-out">
    
    {{-- Logo Container with Pulse Animation --}}
    <div class="relative mb-8 animate-pulse">
        <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" 
             alt="Loading..." 
             class="h-12 w-auto object-contain">
    </div>

    {{-- Progress Bar --}}
    <div class="w-48 h-1 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full bg-[var(--gold)] animate-[progress_1.5s_ease-in-out_infinite] w-full origin-left scale-x-0"></div>
    </div>
</div>

<style>
    @keyframes progress {
        0% { transform: scaleX(0); transform-origin: left; }
        50% { transform: scaleX(0.5); transform-origin: left; }
        100% { transform: scaleX(1); transform-origin: left; opacity: 0; }
    }
</style>

<script>
    // FADE OUT ON LOAD
    window.addEventListener('load', function() {
        const loader = document.getElementById('global-loader');
        if (loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500); // Wait for transition
            }, 500); // Minimum view time
        }
    });

    // FADE IN ON EXIT (Link Clicks)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const target = this.getAttribute('target');
                
                // Ignore internal anchors, javascript links, or external tabs
                if (!href || href.startsWith('#') || href.startsWith('javascript') || target === '_blank' || e.ctrlKey || e.metaKey) {
                    return;
                }

                // Show loader
                const loader = document.getElementById('global-loader');
                if (loader) {
                    loader.style.display = 'flex';
                    // Force reflow
                    void loader.offsetWidth; 
                    loader.style.opacity = '1';
                }
            });
        });
    });
</script>
