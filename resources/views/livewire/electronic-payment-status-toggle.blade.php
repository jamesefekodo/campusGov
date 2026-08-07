<div>
    @if ($saved)
        <div class="alert alert-success py-1 px-3 mb-2 small"
            x-data x-init="setTimeout(() => $el.remove(), 2500)">
            <i class="fas fa-check-circle me-1"></i> Status updated.
        </div>
    @endif

    {{-- <div wire:click="toggle" style="cursor:pointer; display:inline-flex; align-items:center; gap:10px;"> --}}
        <div style="display:inline-flex; align-items:center; gap:10px; opacity:.6; cursor:not-allowed; pointer-events:none;">

        {{-- Switch --}}
        <div style="
            width: 48px; height: 26px; border-radius: 13px;
            background: {{ $is_active ? '#198754' : '#adb5bd' }};
            position: relative; transition: background 0.3s;
        ">
            <div style="
                width: 20px; height: 20px; border-radius: 50%;
                background: #fff; position: absolute; top: 3px;
                left: {{ $is_active ? '25px' : '3px' }};
                transition: left 0.3s;
                box-shadow: 0 1px 3px rgba(0,0,0,.3);
            "></div>
        </div>

        {{-- Label --}}
        <span class="badge {{ $is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
            {{ $is_active ? 'Active' : 'Inactive' }}
        </span>

    </div>

    <div wire:loading class="text-muted small mt-1">
        <i class="fas fa-spinner fa-spin me-1"></i> Saving...
    </div>
</div>
