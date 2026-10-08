<footer class="app-footer">
    <div class="container footer p-3 text-center" style="font-size: 10px">
        <span class="me-3">
            {{ basicSystemInfo()->footer_plus }}
        </span>
        <span class="position-relative d-inline-block" id="footer-brand-trigger" style="cursor: pointer;"
            title="Click to view project details">
            <span style="color: var(--system-secondary) !important">
                &copy; 2025{{ date('Y') > 2025 ? ' - ' . date('Y') : '' }} - CampusGov (V0.2)
            </span>

            {{-- Larger, Smart Popover / Modal Snippet --}}
            <div id="footer-popover"
                class="card shadow-lg position-absolute bottom-100 start-50 translate-middle-x mb-3 p-3 text-dark bg-white border rounded-3"
                style="display: none; width: 280px; z-index: 1050; font-size: 13px;">
                <div class="card-body p-0 text-center">

                    {{-- Odu Systems branding --}}
                    <div class="mb-2">
                        <img src="{{ asset('system/images/odu_logo.png') }}" alt="Odu Systems"
                            style="height: 36px; object-fit: contain;" onerror="this.style.display='none'">
                    </div>
                    <h6 class="fw-bold mb-1">Odu Systems</h6>
                    <p class="text-muted mb-3" style="font-size: 12px;">
                        Web apps, mobile solutions, system design & hardware integration.
                    </p>
                    <a href="https://odusystems.com.ng" target="_blank"
                        class="btn btn-sm btn-dark w-100 py-2 fw-semibold mb-2">
                        <i class="fas fa-globe me-1"></i> Visit odusystems.com.ng
                    </a>

                    {{-- Divider --}}
                    <div class="border-top my-2"></div>

                    {{-- GitHub --}}
                    <p class="text-muted mb-2" style="font-size: 11px;">
                        CampusGov — open source student association platform
                    </p>
                    <a href="https://github.com/jamesefekodo/campusGov" target="_blank"
                        class="btn btn-sm btn-outline-dark w-100 py-2 fw-semibold">
                        <i class="fab fa-github me-1"></i> View on GitHub
                    </a>

                </div>
            </div>
        </span>
    </div>
</footer><!--//app-footer-->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('footer-brand-trigger');
        const popover = document.getElementById('footer-popover');

        if (trigger && popover) {
            // Toggle on click for mobile/desktop friendliness
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                popover.style.display = popover.style.display === 'block' ? 'none' : 'block';
            });

            // Hide when clicking outside
            document.addEventListener('click', function() {
                popover.style.display = 'none';
            });
        }
    });
</script>
