<div class="d-inline-flex align-items-center gap-2">

    <div wire:click="toggle" style="cursor:pointer; display:inline-flex; align-items:center; gap:8px;">

        <div style="width:44px; height:24px; border-radius:12px; background:{{ $mfa ? '#198754' : '#adb5bd' }}; position:relative; transition:background 0.25s; flex-shrink:0;">
            <div style="width:18px; height:18px; border-radius:50%; background:#fff; position:absolute; top:3px; left:{{ $mfa ? '23px' : '3px' }}; transition:left 0.25s; box-shadow:0 1px 3px rgba(0,0,0,.25);"></div>
        </div>

        <span class="badge {{ $mfa ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
            {{ $mfa ? 'MFA Enabled' : 'MFA Disabled' }}
        </span>

    </div>

    <span wire:loading wire:target="toggle" class="text-muted small">
        <i class="fas fa-spinner fa-spin"></i>
    </span>

    @if ($saved)
        <span class="text-success small" x-data x-init="setTimeout(() => $el.remove(), 2000)">
            <i class="fas fa-check"></i> Saved
        </span>
    @endif

</div>
