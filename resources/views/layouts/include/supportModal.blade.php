<style>
    :root {
        --system-primary: {{ basicSystemInfo()['primary_colour'] ?: '#3b5edd' }};
        --system-secondary: {{ basicSystemInfo()['secondary_colour'] ?: '#7a97ff' }};
        --system-tertiary: {{ basicSystemInfo()['tertiary_colour'] ?: 'var(--system-secondary)' }};
    }
</style>

<div class="smart-support-widget" id="smartSupportWidget">

    {{-- Floating button --}}
    <button type="button" class="ssw-trigger" id="sswTrigger" aria-label="Get help">
        <i class="fas fa-headset ssw-trigger-icon"></i>
        <span class="ssw-pulse-ring"></span>
    </button>

    {{-- Hover reactions --}}
    <div class="ssw-reactions" id="sswReactions">
        <span class="ssw-bubble">Need help?</span>
        <span class="ssw-bubble ssw-bubble-delay">We're here <i class="fas fa-user-headset"></i></span>
    </div>

    {{-- Expanded panel --}}
    <div class="ssw-panel" id="sswPanel">

        {{-- Root menu --}}
        <div class="ssw-view ssw-view-active" data-view="menu">
            <div class="ssw-panel-header">
                <span>How can we help?</span>
                <button type="button" class="ssw-close" id="sswClose" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Odu --}}
            <button type="button" class="ssw-option" data-target="odu">
                <span class="ssw-option-icon ssw-icon-odu">
                    <i class="fas fa-robot"></i>
                </span>
                <span class="ssw-option-text">
                    <strong>Odu</strong>
                    <small>AI assistant</small>
                </span>
                <i class="fas fa-chevron-right ssw-option-arrow"></i>
            </button>

            {{-- WhatsApp Support --}}
            <button type="button" class="ssw-option" data-target="whatsapp">
                <span class="ssw-option-icon ssw-icon-whatsapp">
                    <i class="fab fa-whatsapp"></i>
                </span>
                <span class="ssw-option-text">
                    <strong>WhatsApp Support</strong>
                    <small>Talk to a person</small>
                </span>
                <i class="fas fa-chevron-right ssw-option-arrow"></i>
            </button>
        </div>

        {{-- Odu view --}}
        <div class="ssw-view" data-view="odu">
            <div class="ssw-panel-header">
                <button type="button" class="ssw-back" data-back>
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span>Odu</span>
                <button type="button" class="ssw-close" data-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="ssw-odu-body">
                <div class="ssw-odu-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <p class="ssw-odu-message">
                    Odu isn't quite ready yet - we're still teaching it the ropes.
                </p>
                <p class="ssw-odu-submessage">
                    In the meantime, WhatsApp Support can help right away.
                </p>
            </div>
        </div>

        {{-- WhatsApp view --}}
        <div class="ssw-view" data-view="whatsapp">
            <div class="ssw-panel-header">
                <button type="button" class="ssw-back" data-back>
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span>WhatsApp Support</span>
                <button type="button" class="ssw-close" data-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="ssw-line-list">
                @forelse (whatsappSupportLines() as $line)
                    @if ($line->is_active)
                        <button type="button"
                            class="ssw-line"
                            onclick="sswOpenWhatsApp(event, '{{ $line->number }}', '{{ addslashes(basicSystemInfo()->association_acronym ?? config('app.name')) }} :: Support')">
                            <span class="ssw-line-icon">
                                <i class="fab fa-whatsapp"></i>
                            </span>
                            <span>{{ $line->name }}</span>
                            <i class="fas fa-chevron-right ssw-option-arrow"></i>
                        </button>
                    @endif
                @empty
                    <div class="ssw-empty">
                        <i class="fas fa-circle-info me-2"></i>
                        No support lines are available right now.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<style>
    .smart-support-widget {
        position: fixed;
        bottom: 40px;
        right: 24px;
        z-index: 1050;
        font-family: inherit;
    }

    /* Trigger button */
    .ssw-trigger {
        position: relative;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(145deg, var(--system-secondary), var(--system-primary));
        box-shadow: 0 10px 24px rgba(0,0,0,0.25), inset 0 1px 1px rgba(255,255,255,0.2);
        color: #fff;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .ssw-trigger:hover {
        transform: translateY(-3px) scale(1.04);
    }

    .ssw-trigger:active {
        transform: scale(0.94);
    }

    .ssw-trigger-icon {
        position: relative;
        z-index: 2;
    }

    .ssw-pulse-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid var(--system-secondary);
        opacity: 0.6;
        animation: sswPulse 2.4s ease-out infinite;
    }

    @keyframes sswPulse {
        0%   { transform: scale(1);   opacity: 0.7; }
        80%  { transform: scale(1.5); opacity: 0; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    /* Hover reactions */
    .ssw-reactions {
        position: absolute;
        bottom: 74px;
        right: 8px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .smart-support-widget:hover .ssw-reactions {
        opacity: 1;
    }

    .ssw-bubble {
        background: #fff;
        color: var(--system-primary);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        white-space: nowrap;
        animation: sswFloat 2.6s ease-in-out infinite;
    }

    .ssw-bubble-delay {
        animation-delay: 0.4s;
    }

    @keyframes sswFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-5px); }
    }

    /* Panel */
    .ssw-panel {
        position: absolute;
        bottom: 74px;
        right: 0;
        width: 300px;
        background: #F4F6FA;
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.22);
        overflow: hidden;
        transform: scale(0.9) translateY(8px);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.18s ease, opacity 0.18s ease;
    }

    .smart-support-widget.ssw-open .ssw-panel {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    /* Headers */
    .ssw-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: var(--system-primary);
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 10px;
    }

    .ssw-close, .ssw-back {
        background: none;
        border: none;
        color: rgba(255,255,255,0.85);
        font-size: 0.9rem;
        cursor: pointer;
        padding: 4px;
    }

    .ssw-close:hover, .ssw-back:hover {
        color: #fff;
    }

    .ssw-view {
        display: none;
        padding: 10px;
    }

    .ssw-view-active {
        display: block;
    }

    /* Options (Odu, WhatsApp) - simple light style */
    .ssw-option {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 8px;
        cursor: pointer;
        text-align: left;
        color: #111827;
        font-size: 0.9rem;
        transition: background 0.15s ease, border-color 0.15s ease, transform 0.12s ease;
    }

    .ssw-option:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        transform: translateX(3px);
    }

    .ssw-option-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .ssw-icon-odu { background: var(--system-tertiary); }
    .ssw-icon-whatsapp { background: #25D366; }

    .ssw-option-text {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }

    .ssw-option-text small {
        color: #6b7280;
        font-size: 0.75rem;
    }

    .ssw-option-arrow {
        margin-left: auto;
        color: #9ca3af;
        font-size: 0.8rem;
    }

    /* Odu body */
    .ssw-odu-body {
        text-align: center;
        padding: 18px 12px 22px;
    }

    .ssw-odu-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--system-tertiary);
        color: #fff;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
    }

    .ssw-odu-message {
        font-weight: 600;
        color: var(--system-primary);
        margin-bottom: 4px;
    }

    .ssw-odu-submessage {
        font-size: 0.85rem;
        color: #6b7280;
        margin: 0;
    }

    /* WhatsApp contact list */
    .ssw-line-list {
        max-height: 260px;
        overflow-y: auto;
    }

    .ssw-line {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 6px;
        cursor: pointer;
        text-align: left;
        color: #111827;
        font-size: 0.9rem;
        font-weight: 500;
        transition: background 0.15s ease;
    }

    .ssw-line:hover {
        background: #f3f4f6;
    }

    .ssw-line i.fa-whatsapp {
        color: #25D366;
        font-size: 1rem;
    }

    .ssw-empty {
        text-align: center;
        color: #6b7280;
        font-size: 0.85rem;
        padding: 20px 8px;
    }

    @media (max-width: 480px) {
        .ssw-panel {
            width: calc(100vw - 32px);
            right: -8px;
        }
    }
</style>

<script>
    (function () {
        const widget = document.getElementById('smartSupportWidget');
        const trigger = document.getElementById('sswTrigger');
        const panel = document.getElementById('sswPanel');

        function showView(name) {
            panel.querySelectorAll('.ssw-view').forEach(v => v.classList.remove('ssw-view-active'));
            panel.querySelector(`[data-view="${name}"]`).classList.add('ssw-view-active');
        }

        trigger.addEventListener('click', () => {
            widget.classList.toggle('ssw-open');
            if (widget.classList.contains('ssw-open')) {
                showView('menu');
            }
        });

        panel.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => widget.classList.remove('ssw-open'));
        });
        document.getElementById('sswClose').addEventListener('click', () => widget.classList.remove('ssw-open'));

        panel.querySelectorAll('[data-back]').forEach(btn => {
            btn.addEventListener('click', () => showView('menu'));
        });

        panel.querySelectorAll('.ssw-option').forEach(btn => {
            btn.addEventListener('click', () => showView(btn.dataset.target));
        });

        document.addEventListener('click', (e) => {
            if (!widget.contains(e.target)) {
                widget.classList.remove('ssw-open');
            }
        });
    })();

    function sswFormatToWhatsAppNumber(rawNumber) {
        let number = rawNumber.trim();
        let hasPlus = number.startsWith('+');
        number = number.replace(/[^\d]/g, '');

        if (hasPlus || number.startsWith('234')) {
            return number;
        }
        if (number.startsWith('0') && number.length === 11) {
            return '234' + number.substring(1);
        }
        if (number.length === 10) {
            return '234' + number;
        }
        return number;
    }

    function sswOpenWhatsApp(event, rawNumber, introMessage) {
        event.preventDefault();
        const formatted = sswFormatToWhatsAppNumber(rawNumber);
        const encodedMessage = encodeURIComponent(introMessage);
        window.open(`https://wa.me/${formatted}?text=${encodedMessage}`, '_blank');
    }
</script>
