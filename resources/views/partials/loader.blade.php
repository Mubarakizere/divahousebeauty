<div id="global-loader" 
     style="display:none; opacity:0;"
     class="fixed inset-0 z-[9999] bg-white w-screen h-screen flex flex-col items-center justify-center transition-opacity duration-500 ease-in-out">
    
    {{-- Logo Container with Pulse Animation --}}
    <div class="relative animate-pulse mb-6">
        <img src="{{ asset('assets/images/logo-loader.jpg') }}" 
             alt="Loading..." 
             style="max-height: 80px; width: auto;"
             class="object-contain">
    </div>

    {{-- Progress Bar --}}
    <div class="w-16 h-0.5 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full bg-[var(--gold)] animate-[progress_1s_ease-in-out_infinite] w-full origin-left scale-x-0"></div>
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
    (function() {
        const loader = document.getElementById('global-loader');
        if (!loader) return;

        let safetyTimeout = null;

        function hideLoader() {
            if (safetyTimeout) {
                clearTimeout(safetyTimeout);
                safetyTimeout = null;
            }
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500); // Wait for fade transition
        }

        function showLoader() {
            loader.style.display = 'flex';
            void loader.offsetWidth; // Force reflow
            loader.style.opacity = '1';

            // Safety fallback: auto-hide after 5s if navigation stalls or fails
            if (safetyTimeout) clearTimeout(safetyTimeout);
            safetyTimeout = setTimeout(hideLoader, 5000);
        }

        // Ensure loader is hidden on page load / bfcache restore
        window.addEventListener('pageshow', function() {
            hideLoader();
        });

        // Clean up when leaving the page
        window.addEventListener('pagehide', function() {
            hideLoader();
        });

        // Show loader on internal link navigation only
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                // Skip if click event was already prevented
                if (e.defaultPrevented) return;

                // Ignore anchors, javascript links, external tabs, mailto/tel, downloads, modifier keys
                if (
                    !href || 
                    href.startsWith('#') || 
                    href.startsWith('javascript:') || 
                    href.startsWith('mailto:') || 
                    href.startsWith('tel:') || 
                    target === '_blank' || 
                    link.hasAttribute('download') ||
                    e.ctrlKey || e.metaKey || e.shiftKey || e.altKey
                ) {
                    return;
                }

                // Check if target URL is identical to current page
                try {
                    const currentUrl = new URL(window.location.href);
                    const targetUrl = new URL(link.href, window.location.href);
                    if (
                        currentUrl.origin === targetUrl.origin &&
                        currentUrl.pathname === targetUrl.pathname &&
                        currentUrl.search === targetUrl.search
                    ) {
                        return; // Same page navigation, skip loader
                    }
                } catch (err) {}

                // Show loader on page transition
                showLoader();
            });
        });
    })();
</script>
