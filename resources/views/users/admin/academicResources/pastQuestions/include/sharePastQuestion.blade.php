{{-- sharing start --}}
@php
    $pastQuestion = $data['pastQuestion'];
    $publicUrl = route('sharePastQuestion', ['uuid' => $pastQuestion->uuid]);
    $shareTitle = $pastQuestion->header ?? 'Check this out';
@endphp

<div class="engagement-bar" data-url="{{ $publicUrl }}" data-title="{{ $shareTitle }}">

    <div class="share-row">
        <span class="share-label">Share:</span>

        <button type="button" class="share-btn share-native" title="Share" aria-label="Share">
            <i class="fas fa-share-nodes"></i>
        </button>

        <a class="share-btn share-whatsapp" title="Share on WhatsApp" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp"></i>
        </a>

        <a class="share-btn share-facebook" title="Share on Facebook" target="_blank" rel="noopener">
            <i class="fab fa-facebook-f"></i>
        </a>

        <a class="share-btn share-twitter" title="Share on X" target="_blank" rel="noopener">
            <i class="fab fa-x-twitter"></i>
        </a>

        <a class="share-btn share-linkedin" title="Share on LinkedIn" target="_blank" rel="noopener">
            <i class="fab fa-linkedin-in"></i>
        </a>

        <a class="share-btn share-telegram" title="Share on Telegram" target="_blank" rel="noopener">
            <i class="fab fa-telegram"></i>
        </a>

        <button type="button" class="share-btn share-copy" title="Copy link" aria-label="Copy link">
            <i class="fas fa-link"></i>
        </button>
    </div>
</div>

<style>
    .engagement-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        margin: 1.5rem 0;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 14px;
    }

    .engagement-stats {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.85rem;
        color: #495057;
    }

    .stat-pill i {
        color: #4361ee;
        font-size: 0.8rem;
    }

    .stat-value {
        font-weight: 700;
        color: #212529;
    }

    .stat-label {
        color: #868e96;
    }

    .share-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .share-label {
        font-size: 0.85rem;
        color: #868e96;
        margin-right: 2px;
    }

    .share-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        color: #fff;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .share-btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
        color: #fff;
    }

    .share-native {
        background: #4361ee;
    }

    .share-whatsapp {
        background: #25D366;
    }

    .share-facebook {
        background: #1877F2;
    }

    .share-twitter {
        background: #000000;
    }

    .share-linkedin {
        background: #0A66C2;
    }

    .share-telegram {
        background: #26A5E4;
    }

    .share-copy {
        background: #495057;
    }

    .share-copy.copied {
        background: #2ec4b6;
    }

    .share-copy.copied::after {
        content: 'Copied!';
        position: absolute;
        margin-top: -34px;
        background: #212529;
        color: #fff;
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }

    @media (max-width: 480px) {
        .engagement-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .share-row {
            justify-content: center;
        }

        .engagement-stats {
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bar = document.querySelector('.engagement-bar');
        if (!bar) return;

        const shareUrl = bar.dataset.url;
        const shareTitle = bar.dataset.title;
        const encodedUrl = encodeURIComponent(shareUrl);
        const encodedTitle = encodeURIComponent(shareTitle);

        // Populate platform share links
        bar.querySelector('.share-whatsapp').href = `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`;
        bar.querySelector('.share-facebook').href =
            `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
        bar.querySelector('.share-twitter').href =
            `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`;
        bar.querySelector('.share-linkedin').href =
            `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
        bar.querySelector('.share-telegram').href =
            `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`;

        function recordShare() {
            const countEl = document.getElementById('shareCountDisplay');
            if (countEl) {
                countEl.textContent = (parseInt(countEl.textContent.replace(/,/g, '')) + 1).toLocaleString();
            }

        }

        // Native share (mobile-friendly)
        const nativeBtn = bar.querySelector('.share-native');
        if (navigator.share) {
            nativeBtn.addEventListener('click', function() {
                navigator.share({
                        title: shareTitle,
                        url: shareUrl
                    })
                    .then(recordShare)
                    .catch(() => {});
            });
        } else {
            nativeBtn.style.display = 'none';
        }

        // Track clicks on platform links too
        bar.querySelectorAll(
                '.share-whatsapp, .share-facebook, .share-twitter, .share-linkedin, .share-telegram')
            .forEach(function(link) {
                link.addEventListener('click', recordShare);
            });

        // Copy link
        const copyBtn = bar.querySelector('.share-copy');
        copyBtn.style.position = 'relative';
        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(shareUrl).then(function() {
                copyBtn.classList.add('copied');
                copyBtn.querySelector('i').classList.replace('fa-link', 'fa-check');
                setTimeout(function() {
                    copyBtn.classList.remove('copied');
                    copyBtn.querySelector('i').classList.replace('fa-check', 'fa-link');
                }, 1800);
            });
        });
    });
</script>
{{-- sharing end --}}
