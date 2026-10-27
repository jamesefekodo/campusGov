@extends('users.guests.themes.matrix.layout.matrixLayout')

@section('title', 'About Us')

@section('content')

    <style>
        /* =========================================================
                                                                               MATRIX SHARED COMPONENTS
                                                                               Use across Home, About, Contact, Events, etc.
                                                                            ========================================================= */

        .mx-page {
            --mx-primary: #087f8c;
            --mx-primary-dark: #06636d;
            --mx-accent: #1696e7;
            --mx-ink: #172b3a;
            --mx-body: #536575;
            --mx-muted: #718096;
            --mx-light: #f4f8fb;
            --mx-border: #e2eaf0;
            --mx-dark: #10212d;
            --mx-dark-card: #172d3a;

            color: var(--mx-ink);
            background: #ffffff;
            font-size: 15px;
            line-height: 1.8;
        }

        .mx-page *,
        .mx-page *::before,
        .mx-page *::after {
            box-sizing: border-box;
        }

        .mx-page .container {
            max-width: 1180px;
        }

        .mx-page a {
            text-decoration: none;
        }

        .mx-page a:focus-visible {
            outline: 3px solid var(--theme-accent);
            outline-offset: 4px;
        }

        .mx-eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 8px 15px;
            color: #bdeeff;
            background: rgb(from var(--theme-primary) r g b / 0.12);
            border: 1px solid rgb(from var(--theme-primary) r g b / 0.25);
            border-radius: 50px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.7px;
            text-transform: uppercase;
        }

        .mx-hero h1 {
            max-width: 900px;
            margin: 25px auto 20px;
            color: #ffffff;
            font-size: clamp(38px, 6vw, 68px);
            font-weight: 850;
            letter-spacing: -2.3px;
            line-height: 1.1;
        }

        .mx-hero h1 span {
            color: color-mix(in srgb, var(--theme-primary) 35%, white);
        }

        .mx-hero-description {
            max-width: 680px;
            margin: 0 auto;
            color: #c7d7e2;
            font-size: 16px;
            line-height: 2;
        }

        .mx-hero-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px 25px;
            margin-top: 30px;
            color: #d8e6ed;
            font-size: 12px;
        }

        .mx-hero-details span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .mx-hero-details i {
            color: color-mix(in srgb, var(--theme-primary) 35%, white);
        }

        /* Sections */

        .mx-section {
            padding: 82px 0;
        }

        .mx-section-white {
            color: var(--mx-ink);
            background: #ffffff;
        }

        .mx-section-light {
            color: var(--mx-ink);
            background: #f3f7fa;
        }

        .mx-section-dark {
            color: #ffffff;
            background:
                radial-gradient(ellipse at 0% 0%,
                    rgb(from var(--theme-primary) r g b / 0.10),
                    transparent 38%),
                #10212d;
        }

        .mx-section-heading {
            max-width: 730px;
            margin: 0 auto 42px;
        }

        .mx-kicker {
            display: inline-block;
            margin-bottom: 10px;
            color: var(--theme-primary);
            font-size: 11px;
            font-weight: 850;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .mx-section-dark .mx-kicker {
            color: color-mix(in srgb, var(--theme-primary) 35%, white);
        }

        .mx-section-heading h2 {
            margin: 0 0 15px;
            color: var(--mx-ink);
            font-size: clamp(28px, 3.6vw, 42px);
            font-weight: 800;
            letter-spacing: -1.2px;
            line-height: 1.25;
        }

        .mx-section-dark .mx-section-heading h2 {
            color: #ffffff;
        }

        .mx-section-heading p {
            margin: 0;
            color: var(--mx-body);
            font-size: 15px;
            line-height: 1.95;
        }

        .mx-section-dark .mx-section-heading p {
            color: color-mix(in srgb, var(--theme-primary) 30%, #c7d7e2);
        }

        /* Copy and prose */

        .mx-copy {
            height: 100%;
            padding: 8px 25px 8px 0;
        }

        .mx-copy h2 {
            margin: 0 0 22px;
            color: var(--mx-ink);
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.3;
        }

        .mx-copy h2 span {
            color: var(--theme-primary);
        }

        .mx-prose {
            color: var(--mx-body);
            font-size: 15px;
            line-height: 2;
            overflow-wrap: anywhere;
            text-align: justify;
        }

        .mx-prose p:last-child {
            margin-bottom: 0;
        }

        /* Feature panel */

        .mx-feature-panel {
            position: relative;
            height: 100%;
            padding: 34px;
            overflow: hidden;
            background: #f0f8fa;
            border: 1px solid #dceef0;
            border-radius: 22px;
        }

        .mx-feature-panel::after {
            position: absolute;
            right: -45px;
            bottom: -70px;
            width: 190px;
            height: 190px;
            content: "";
            border: 30px solid rgba(8, 127, 140, .05);
            border-radius: 50%;
            pointer-events: none;
        }

        .mx-feature-item {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .mx-feature-item+.mx-feature-item {
            margin-top: 28px;
            padding-top: 28px;
            border-top: 1px solid #d7e8eb;
        }

        .mx-feature-icon {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 49px;
            height: 49px;
            color: var(--theme-primary);
            background: #ffffff;
            border: 1px solid #dcecef;
            border-radius: 15px;
            font-size: 18px;
        }

        .mx-feature-item h3 {
            margin: 0 0 7px;
            color: var(--mx-ink);
            font-size: 16px;
            font-weight: 800;
        }

        .mx-feature-item p {
            margin: 0;
            color: var(--mx-body);
            font-size: 13px;
            line-height: 1.9;
        }

        /* President remarks — editorial magazine layout */

        .mx-president-panel {
            position: relative;
            padding: clamp(28px, 4vw, 58px);
            overflow: hidden;
            background:
                radial-gradient(circle at 100% 0%, rgba(22, 150, 231, .15), transparent 42%),
                linear-gradient(135deg, #172f3d, #10212d);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 30px;
        }

        .mx-president-layout {
            display: grid;
            grid-template-columns: minmax(250px, 320px) minmax(0, 1fr);
            grid-template-areas:
                "portrait lead"
                "remaining remaining";
            gap: clamp(28px, 4vw, 52px);
            align-items: start;
        }

        .mx-president-portrait {
            grid-area: portrait;
            max-width: 320px;
        }

        .mx-president-portrait-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 26px;
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(255, 255, 255, .06);
        }

        .mx-president-portrait-frame::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(180deg,
                    transparent 58%,
                    rgba(16, 33, 45, .55));
            pointer-events: none;
        }

        .mx-president-portrait-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        .mx-president-portrait-details {
            margin-top: 20px;
            color: #ffffff;
        }

        .mx-president-portrait-details strong {
            display: block;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .mx-president-portrait-details span {
            display: block;
            margin-top: 5px;
            color: #9fd9e8;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .mx-president-portrait-details small {
            display: block;
            margin-top: 5px;
            color: #b9cad5;
            font-size: 12px;
        }

        .mx-president-lead {
            grid-area: lead;
            align-self: center;
        }

        .mx-president-lead .mx-quote-icon {
            margin-bottom: 20px;
        }

        .mx-president-lead h2 {
            margin: 0 0 18px;
            color: #ffffff;
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 800;
            letter-spacing: -.9px;
            line-height: 1.2;
        }

        .mx-president-lead-text {
            color: #d0dce5;
            font-size: 16px;
            line-height: 1.95;
            text-align: justify;
        }

        .mx-president-lead-text p {
            margin: 0;
        }

        .mx-president-remaining {
            grid-area: remaining;
            padding-top: clamp(24px, 3vw, 34px);
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        .mx-president-remaining .mx-quote-body {
            max-width: 980px;
            color: #c9d6df;
            font-size: 15px;
            line-height: 2;
        }

        .mx-president-remaining .mx-quote-body p {
            margin: 0 0 18px;
        }

        .mx-president-remaining .mx-quote-body p:last-child {
            margin-bottom: 0;
        }

        .mx-president-signature {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 30px;
        }

        .mx-president-signature strong {
            display: block;
            color: #ffffff;
            font-size: 14px;
        }

        .mx-president-signature small {
            display: block;
            margin-top: 3px;
            color: #b9cad5;
            font-size: 12px;
        }

        /* Large screens: give the layout more breathing room */
        @media (min-width: 1400px) {
            .mx-president-layout {
                grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
                gap: 60px;
            }

            .mx-president-portrait {
                max-width: 360px;
            }

            .mx-president-lead-text {
                font-size: 17px;
            }
        }

        /* Tablet */
        @media (max-width: 991px) {
            .mx-president-layout {
                grid-template-columns: minmax(0, 1fr);
                grid-template-areas:
                    "portrait"
                    "lead"
                    "remaining";
            }

            .mx-president-portrait {
                max-width: 280px;
                margin: 0 auto;
                text-align: center;
            }

            .mx-president-lead {
                text-align: center;
            }

            .mx-president-lead .mx-quote-icon {
                margin-left: auto;
                margin-right: auto;
            }

            .mx-president-remaining .mx-quote-body {
                max-width: 760px;
                margin: 0 auto;
                text-align: center;
            }

            .mx-president-signature {
                justify-content: center;
            }
        }

        /* Mobile */
        @media (max-width: 767px) {
            .mx-president-panel {
                padding: 22px;
                border-radius: 22px;
            }

            .mx-president-portrait {
                max-width: 230px;
            }

            .mx-president-lead h2 {
                font-size: clamp(27px, 8vw, 34px);
            }

            .mx-president-lead-text,
            .mx-president-remaining .mx-quote-body {
                font-size: 14px;
            }
        }

        /* Profile cards */

        .mx-profile-card {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 355px;
            padding: 24px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e3eaf0;
            border-radius: 21px;
            box-shadow: 0 8px 26px rgba(25, 55, 75, .045);
            transition: all .25s ease;
        }

        .mx-profile-card:hover {
            transform: translateY(-6px);
            border-color: #b8dce4;
            box-shadow: 0 20px 42px rgba(25, 55, 75, .11);
        }

        .mx-profile-card-alt {
            background: #f7fafc;
        }

        .mx-profile-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 25px;
        }

        .mx-profile-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 10px;
            color: var(--theme-primary);
            background: #edf7f8;
            border-radius: 7px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .mx-profile-number {
            color: #9aaab7;
            font-size: 12px;
            font-weight: 800;
        }

        .mx-profile-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 82px;
            height: 82px;
            margin-bottom: 22px;
            color: var(--theme-primary);
            background: linear-gradient(145deg, #eaf7f9, #f5f9fc);
            border: 1px solid #d8ecef;
            border-radius: 23px;
            font-size: 30px;
        }

        .mx-profile-card-alt .mx-profile-avatar {
            border-radius: 50%;
            background: #ffffff;
        }

        .mx-profile-role {
            align-self: flex-start;
            margin-bottom: 10px;
            padding: 5px 9px;
            color: var(--theme-background);
            background: rgb(from var(--theme-primary) r g b / 0.10);
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
        }

        .mx-profile-card h3 {
            margin: 0 0 10px;
            color: #182d3c;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.45;
        }

        .mx-profile-description {
            flex: 1;
            margin: 0 0 22px;
            color: #627384;
            font-size: 13px;
            line-height: 1.9;
        }

        .mx-profile-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #edf0f3;
            color: #7c8b98;
            font-size: 10px;
        }

        .mx-placeholder {
            color: #946515;
            font-weight: 700;
        }

        /* Contact cards */

        .mx-contact-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            height: 100%;
            padding: 25px;
            background: #ffffff;
            border: 1px solid #e1eaf0;
            border-radius: 18px;
            transition: all .2s ease;
        }

        .mx-contact-card:hover {
            transform: translateY(-4px);
            border-color: #b7dce3;
        }

        .mx-contact-icon {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            color: var(--theme-primary);
            background: #edf7f8;
            border-radius: 14px;
            font-size: 17px;
        }

        .mx-contact-card h3 {
            margin: 2px 0 7px;
            color: var(--mx-ink);
            font-size: 14px;
            font-weight: 800;
        }

        .mx-contact-card p,
        .mx-contact-card a {
            margin: 0;
            color: var(--mx-body);
            font-size: 13px;
            line-height: 1.85;
            overflow-wrap: anywhere;
        }

        .mx-contact-card a:hover {
            color: var(--theme-primary);
        }

        /* CTA and buttons */

        .mx-cta {
            position: relative;
            padding: clamp(28px, 5vw, 52px);
            overflow: hidden;
            text-align: center;
            background:
                radial-gradient(circle at 100% 0%, rgba(22, 150, 231, .13), transparent 40%),
                #f0f7fa;
            border: 1px solid #dceaf0;
            border-radius: 25px;
        }

        .mx-cta h2 {
            margin: 0 0 14px;
            color: var(--mx-ink);
            font-size: clamp(27px, 3.6vw, 40px);
            font-weight: 850;
            letter-spacing: -1px;
        }

        .mx-cta p {
            max-width: 650px;
            margin: 0 auto;
            color: var(--mx-body);
            font-size: 14px;
            line-height: 1.95;
        }

        .mx-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 27px;
        }

        .mx-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 47px;
            padding: 12px 20px;
            color: #ffffff;
            background: var(--theme-primary);
            border: 1px solid var(--theme-primary);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            transition: all .2s ease;
        }

        .mx-button:hover {
            color: #ffffff;
            background: var(--theme-background);
            border-color: var(--theme-background);
            transform: translateY(-2px);
        }

        .mx-button-outline {
            color: var(--theme-background);
            background: #ffffff;
            border-color: #cbdfe6;
        }

        .mx-button-outline:hover {
            color: #ffffff;
            background: var(--theme-primary);
            border-color: var(--theme-primary);
        }

        /* Responsive */

        @media (max-width: 991px) {
            .mx-hero {
                padding-top: 130px;
                padding-bottom: 75px;
            }

            .mx-section {
                padding: 65px 0;
            }

            .mx-copy {
                padding-right: 0;
            }

            .mx-profile-card {
                min-height: 340px;
            }
        }

        @media (max-width: 767px) {
            .mx-page {
                font-size: 14px;
            }

            .mx-hero {
                padding: 115px 0 58px;
            }

            .mx-hero h1 {
                font-size: clamp(36px, 10vw, 49px);
                letter-spacing: -1.5px;
            }

            .mx-section {
                padding: 48px 0;
            }

            .mx-section-heading {
                margin-bottom: 28px;
            }

            .mx-feature-panel,
            .mx-quote-panel,
            .mx-profile-card,
            .mx-contact-card,
            .mx-cta {
                padding: 22px;
            }

            .mx-profile-card {
                min-height: 0;
            }

            .mx-profile-avatar {
                width: 70px;
                height: 70px;
                border-radius: 19px;
                font-size: 25px;
            }

            .mx-profile-card h3 {
                font-size: 17px;
            }

            .mx-button {
                width: 100%;
            }

            .mx-actions {
                align-items: stretch;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .mx-page *,
            .mx-page *::before,
            .mx-page *::after {
                transition: none !important;
                animation: none !important;
                scroll-behavior: auto !important;
            }
        }

        .association-leadership-page {
            --leadership-green: #006a4e;
            --leadership-navy: #002147;
            --leadership-blue: #1696e7;
            --leadership-muted: #687780;
            --leadership-border: #e5ece8;
            color: #25352f;
        }

        .leadership-section {
            margin-bottom: 48px;
        }

        .leadership-section>.d-flex {
            padding-bottom: 16px;
            border-bottom: 1px solid var(--leadership-border);
        }

        .leadership-section h4 {
            color: var(--leadership-navy);
            font-size: 1.25rem;
            font-weight: 750;
            letter-spacing: -0.02em;
        }

        .leadership-section-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8f4ef;
            color: var(--leadership-green);
            border-radius: 14px;
            font-size: 19px;
        }

        .advisor-icon {
            background: #eaf0f8;
            color: var(--leadership-navy);
        }

        .leadership-arm {
            margin-top: 24px;
        }

        .leadership-arm-heading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin: 24px 0 18px;
            text-align: center;
        }

        .leadership-arm-heading h5 {
            color: var(--leadership-green);
            font-weight: 750;
            font-size: 1.02rem;
        }

        .leadership-arm-line {
            height: 1px;
            background: #dce7e1;
            flex: 1;
            max-width: 150px;
        }

        .leader-profile-card,
        .advisor-profile-card {
            height: 100%;
            min-width: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--leadership-border);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 4px 16px rgba(20, 45, 35, 0.05);
            transition: transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .leader-profile-card:hover,
        .advisor-profile-card:hover {
            transform: translateY(-4px);
            border-color: #cbded4;
            box-shadow: 0 14px 30px rgba(20, 45, 35, 0.10);
        }

        .leader-photo,
        .advisor-photo {
            width: 100%;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #f4f7f5;
            border-bottom: 1px solid #edf1ee;
        }

        .leader-photo img,
        .advisor-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .leader-profile-details,
        .advisor-details {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 16px 16px 15px;
        }

        .leader-name,
        .advisor-details h6 {
            color: #243a31;
            font-size: 0.98rem;
            line-height: 1.35;
            font-weight: 750;
            margin: 0 0 6px;
            overflow-wrap: anywhere;
        }

        .leader-position,
        .advisor-position {
            display: inline-block;
            max-width: 100%;
            padding: 5px 10px;
            margin-bottom: 10px;
            border-radius: 999px;
            background: #eaf5ef;
            color: var(--leadership-green);
            font-size: 0.74rem;
            line-height: 1.3;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .advisor-position {
            background: #edf2f9;
            color: var(--leadership-navy);
        }

        .leader-remarks,
        .advisor-remarks {
            width: 100%;
            color: var(--leadership-muted);
            font-size: 0.83rem;
            line-height: 1.55;
            margin: 0;
            overflow-wrap: anywhere;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .leader-social-links {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: auto;
            padding-top: 14px;
        }

        .leader-social-link {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            border: 1px solid #e2e9e5;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--leadership-green);
            text-decoration: none;
            background: #fff;
            transition: background 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease;
        }

        .leader-social-link:hover {
            background: var(--leadership-green);
            border-color: var(--leadership-green);
            color: #fff;
        }

        .leader-social-link img {
            width: 18px;
            height: 18px;
            object-fit: contain;
        }

        .advisor-service-period {
            width: 100%;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid #edf1ee;
            color: var(--leadership-muted);
            font-size: 0.78rem;
        }

        .leadership-empty-state {
            padding: 34px 20px;
            text-align: center;
            border: 1px dashed #cddbd3;
            border-radius: 16px;
            color: #78857e;
            background: #fafcfb;
        }

        .leadership-empty-state h6 {
            color: #33483e;
            font-weight: 700;
        }

        @media (max-width: 575.98px) {
            .leadership-section {
                margin-bottom: 34px;
            }

            .leadership-section-icon {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
                border-radius: 12px;
            }

            .leadership-section h4 {
                font-size: 1.08rem;
            }

            .leader-profile-details,
            .advisor-details {
                padding: 14px 12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .leader-profile-card,
            .advisor-profile-card,
            .leader-social-link {
                transition: none;
            }
        }

        /* Smaller cards on large screens */
        @media (min-width: 992px) {

            .leader-profile-card,
            .advisor-profile-card {
                border-radius: 15px;
            }

            .leader-photo,
            .advisor-photo {
                aspect-ratio: 4 / 5;
            }

            .leader-profile-details,
            .advisor-details {
                padding: 13px 12px 12px;
            }

            .leader-name,
            .advisor-details h6 {
                font-size: 0.9rem;
                margin-bottom: 5px;
            }

            .leader-position,
            .advisor-position {
                padding: 4px 8px;
                font-size: 0.7rem;
                margin-bottom: 8px;
            }

            .leader-remarks,
            .advisor-remarks {
                font-size: 0.78rem;
                line-height: 1.45;
            }

            .leader-social-link {
                width: 30px;
                height: 30px;
                flex: 0 0 30px;
                border-radius: 9px;
            }

            .leader-social-link img {
                width: 16px;
                height: 16px;
            }

            .advisor-service-period {
                font-size: 0.72rem;
                padding-top: 10px;
            }
        }

        /* Even smaller on extra-large screens */
        @media (min-width: 1200px) {

            .leader-photo,
            .advisor-photo {
                aspect-ratio: 1 / 1;
            }

            .leader-profile-details,
            .advisor-details {
                padding: 12px 11px 11px;
            }

            .leader-name,
            .advisor-details h6 {
                font-size: 0.87rem;
            }

            .leader-remarks,
            .advisor-remarks {
                font-size: 0.75rem;
            }
        }
    </style>

    @php
        $basicInfo = $data['basicInfo'] ?? null;
        $systemInfo = basicSystemInfo();

        $associationName = $systemInfo->association_name ?? ($systemInfo->association_acronym ?? config('app.name'));

        $associationAcronym = $systemInfo->association_acronym ?? config('app.name');

        $schoolName = $systemInfo->school_name ?? ($systemInfo->school_acronym ?? '');

        $leaders = [
            [
                'name' => 'Association President',
                'position' => 'President',
                'icon' => 'fa-user-tie',
                'description' =>
                    'Provides strategic leadership, represents members, and guides the association towards its objectives.',
            ],
            [
                'name' => 'Vice President',
                'position' => 'Vice President',
                'icon' => 'fa-user-shield',
                'description' => 'Supports the president and coordinates assigned association responsibilities.',
            ],
            [
                'name' => 'General Secretary',
                'position' => 'General Secretary',
                'icon' => 'fa-file-lines',
                'description' =>
                    'Coordinates official correspondence, meetings, records, and association documentation.',
            ],
            [
                'name' => 'Financial Secretary',
                'position' => 'Financial Secretary',
                'icon' => 'fa-coins',
                'description' => 'Maintains financial records and supports transparent financial administration.',
            ],
            [
                'name' => 'Public Relations Officer',
                'position' => 'Public Relations',
                'icon' => 'fa-bullhorn',
                'description' => 'Communicates association announcements, activities, and important updates.',
            ],
            [
                'name' => 'Director of Welfare',
                'position' => 'Welfare Director',
                'icon' => 'fa-handshake-angle',
                'description' => 'Promotes member welfare, inclusion, and access to association support.',
            ],
        ];

        $advisors = [
            [
                'name' => 'Association Patron',
                'position' => 'Patron / Patroness',
                'icon' => 'fa-award',
            ],
            [
                'name' => 'Staff Adviser',
                'position' => 'Staff Adviser',
                'icon' => 'fa-chalkboard-user',
            ],
            [
                'name' => 'Advisory Representative',
                'position' => 'Advisory Council',
                'icon' => 'fa-users',
            ],
        ];

        $contactEmail = $basicInfo->email ?? ($basicInfo->contact_email ?? null);

        $contactPhone = $basicInfo->phone ?? ($basicInfo->contact_phone ?? null);

        $contactAddress = $basicInfo->address ?? ($basicInfo->contact_address ?? null);
    @endphp

    <main class="mx-page p-5">

        {{-- ABOUT --}}
        <section class="mx-section mx-section-white">
            <div class="row g-4 align-items-stretch">

                <div class="col-md-12">
                    <div class="mx-copy">
                        {{-- <span class="mx-kicker">Our Story</span> --}}

                        <h2 class="fw-bold">
                            About <span>{{ $associationName }}</span>
                        </h2>

                        <div class="mx-prose">
                            @if (!empty($basicInfo?->description))
                                {!! $basicInfo->description !!}
                            @endif
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-5">
                    <div class="mx-feature-panel">
                        <div class="mx-feature-item">
                            <div class="mx-feature-icon">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>

                            <div>
                                <h3>Our Purpose</h3>
                                <p>
                                    To represent members, encourage participation, and advance
                                    the objectives of the association through responsible leadership.
                                </p>
                            </div>
                        </div>

                        <div class="mx-feature-item">
                            <div class="mx-feature-icon">
                                <i class="fa-solid fa-eye"></i>
                            </div>

                            <div>
                                <h3>Our Vision</h3>
                                <p>
                                    To cultivate a connected, progressive, and engaged community
                                    where members can contribute and grow.
                                </p>
                            </div>
                        </div>

                        <div class="mx-feature-item">
                            <div class="mx-feature-icon">
                                <i class="fa-solid fa-people-group"></i>
                            </div>

                            <div>
                                <h3>Our Community</h3>
                                <p>
                                    To encourage unity, mutual support, shared responsibility,
                                    and meaningful involvement in association activities.
                                </p>
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
        </section>

    @section('presidentData')
        @php
            $president = $data['thePresident'] ?? null;
            $presidentRep = $president?->theRep;
            $presidentProfile = $presidentRep?->memberPersonalProfile;

            $presidentPhoto = asset('system/images/default_profile_pic.webp');

            if (
                $presidentRep?->photo &&
                file_exists(
                    public_path(
                        'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $presidentRep->photo,
                    ),
                )
            ) {
                $presidentPhoto = asset(
                    'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $presidentRep->photo,
                );
            }

            $middleInitial = trim(strtoupper(substr($presidentProfile?->middle_name ?? '', 0, 1)));

            $presidentName = trim(
                ($presidentProfile?->first_name ?? '') .
                    ' ' .
                    ($middleInitial ? $middleInitial . '.' : '') .
                    ' ' .
                    ($presidentProfile?->last_name ?? ''),
            );
        @endphp
    @endsection

    @if (!empty($data['thePresident']))
        @php
            $presidentRep = $data['thePresident']->theRep ?? null;
            $presidentProfile = $presidentRep->memberPersonalProfile ?? null;

            $presidentPhoto = asset('system/images/default_profile_pic.webp');

            if (
                $presidentRep?->photo &&
                file_exists(
                    public_path(
                        'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $presidentRep->photo,
                    ),
                )
            ) {
                $presidentPhoto = asset(
                    'assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $presidentRep->photo,
                );
            }

            $middleInitial = trim(strtoupper(substr($presidentProfile?->middle_name ?? '', 0, 1)));

            $presidentName = trim(
                ($presidentProfile?->first_name ?? '') .
                    ' ' .
                    ($middleInitial ? $middleInitial . '.' : '') .
                    ' ' .
                    ($presidentProfile?->last_name ?? ''),
            );
        @endphp

        <section class="mx-section mx-section-dark">
            <div class="container-xxl">
                <div class="mx-president-panel">

                    <div class="mx-president-layout">

                        {{-- Portrait --}}
                        <div class="mx-president-portrait">
                            <div class="mx-president-portrait-frame">
                                <img src="{{ $presidentPhoto }}" alt="{{ $presidentName ?: 'Association President' }}">
                            </div>

                            <div class="mx-president-portrait-details">
                                <strong>{{ $presidentName ?: 'Association President' }}</strong>

                                <span>
                                    President, {{ strtoupper($associationAcronym) }}
                                </span>

                                @if (!empty($data['currentAcademicYear']))
                                    <small>
                                        {{ $data['currentAcademicYear'] }}/{{ $data['currentAcademicYear'] + 1 }}
                                        Session
                                    </small>
                                @endif
                            </div>
                        </div>

                        {{-- Opening remark beside the portrait --}}
                        <div class="mx-president-lead">
                            <div class="mx-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>

                            <span class="mx-kicker">
                                Message From Our Leadership
                            </span>

                            <h2>President’s Remarks</h2>

                            <div class="mx-quote-body mx-president-lead-text">
                                @php
                                    $remarks = $basicInfo?->president_remarks;

                                    $remarkParagraphs = $remarks
                                        ? preg_split('/\r\n|\r|\n/', trim(strip_tags(html_entity_decode($remarks))))
                                        : [];

                                    $remarkParagraphs = array_values(
                                        array_filter($remarkParagraphs, fn($paragraph) => trim($paragraph) !== ''),
                                    );

                                    $leadRemark = $remarkParagraphs[0] ?? null;
                                    $remainingRemarks = array_slice($remarkParagraphs, 1);
                                @endphp

                                @if ($leadRemark)
                                    <p>{{ $leadRemark }}</p>
                                @else
                                    <p>
                                        Welcome to {{ $associationName }}. Our association is built on
                                        representation, cooperation, responsibility, and service to members.
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Remaining remarks below, full width --}}
                        @if (!empty($remainingRemarks))
                            <div class="mx-president-remaining">
                                <div class="mx-quote-body">
                                    @foreach ($remainingRemarks as $remark)
                                        <p>{{ trim($remark) }}</p>
                                    @endforeach
                                </div>

                                <div class="mx-president-signature">
                                    <div class="mx-quote-avatar">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>

                                    <div>
                                        <strong>{{ $presidentName ?: 'Association President' }}</strong>
                                        <small>
                                            President, {{ strtoupper($associationAcronym) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mx-president-remaining">
                                <div class="mx-president-signature">
                                    <div class="mx-quote-avatar">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>

                                    <div>
                                        <strong>{{ $presidentName ?: 'Association President' }}</strong>
                                        <small>
                                            President, {{ strtoupper($associationAcronym) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </section>
    @endif
    {{-- ############################################################################### --}}

    {{-- ########################################################## --}}
    {{-- LEADERS --}}
    <section class="mx-section mx-section-light">
        <div class="container-fluid px-5">

            <div class="mx-section-heading text-center">
                <span class="mx-kicker">Meet The Team</span>

                <h2>Our Association Leaders</h2>

                <p>
                    Meet the offices responsible for representing members, coordinating
                    activities, and advancing the association’s objectives.
                </p>
            </div>

            <div class="association-leadership-page">

                {{-- STUDENT LEADERS --}}
                <div class="leadership-section mb-5">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        @if (intval($data['academicSession']) !== intval(currentAcademicYear()))
                            <div class="d-flex align-items-center gap-3">
                                <div class="leadership-section-icon">
                                    <i class="fa fa-users"></i>
                                </div>

                                <div>
                                    <h4 class="mb-1">Previous Leadership</h4>

                                    <p class="text-muted mb-0">
                                        Association representatives for the
                                        {{ $data['academicSession'] }}/{{ $data['academicSession'] + 1 }}
                                        academic session
                                    </p>
                                </div>
                            </div>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#sessionSelector">
                            <i class="fa-solid fa-calendar-days me-2"></i>
                            Change Session
                        </button>
                    </div>

                    {{-- ################################################# --}}
                    <div class="modal fade" id="sessionSelector" tabindex="-1" aria-labelledby="sessionSelectorLabel"
                        aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="sessionSelectorLabel">
                                        <i class="fa-solid fa-calendar-days me-2"></i>
                                        Change Academic Session
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>

                                {{-- Changed to plain GET without CSRF token to keep URL clean --}}
                                <form method="GET" action="{{ request()->url() }}">

                                    <div class="modal-body">
                                        <p class="text-muted small mb-3">
                                            Select an academic session to view its leadership tenure.
                                        </p>

                                        <div class="mb-3">
                                            <label for="academic_session" class="form-label fw-semibold">
                                                Academic Session <span class="text-danger">*</span>
                                            </label>

                                            {{-- Fixed: text-center moved to class attribute --}}
                                            <select name="academic_session" id="academic_session"
                                                class="form-select text-center" required>
                                                <option value="" disabled>
                                                    Select academic session
                                                </option>

                                                @php
                                                    $startYear = schoolFoundingYear();
                                                    $endYear = currentAcademicYear();
                                                    $selectedSession = request(
                                                        'academic_session',
                                                        $data['academicSession'] ?? $endYear,
                                                    );
                                                @endphp

                                                @for ($year = $endYear; $year >= $startYear; $year--)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedSession == $year ? 'selected' : '' }}>
                                                        {{ $year }}/{{ $year + 1 }}
                                                        {{ $year == $endYear ? ' — Current Session' : '' }}
                                                    </option>
                                                @endfor
                                            </select>

                                            @error('academic_session')
                                                <div class="text-danger small mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        {{-- Fixed: Removed 'bg-red' from type attribute --}}
                                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-filter me-2"></i>
                                            Apply Session
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    
                    {{-- ########################################################## --}}

                    @php
                        $leadersByArm = $data['studentLeaders']->groupBy(
                            fn($leader) => $leader->position?->arm?->slug ?? 'other',
                        );
                    @endphp

                    @forelse ($leadersByArm as $armLeaders)
                        @php
                            $arm = $armLeaders->first()->position?->arm;
                        @endphp

                        <div class="leadership-arm mb-4">
                            <div class="leadership-arm-heading">
                                <span class="leadership-arm-line"></span>

                                <h5 class="mb-0">
                                    {{ $arm?->name ?? 'Other Representatives' }}
                                </h5>

                                <span class="leadership-arm-line"></span>
                            </div>

                            @if ($arm?->description)
                                <p class="text-muted text-center small mb-4">
                                    {{ $arm->description }}
                                </p>
                            @endif

                            <div class="row g-3">
                                @foreach ($armLeaders as $leader)
                                    @php
                                        $representative = $leader->theRep;

                                        $socialLinks = $representative
                                            ? $representative->memberSocialMediaHandle->filter(
                                                fn($handle) => filled($handle->link),
                                            )
                                            : collect();
                                    @endphp

                                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                        <div class="leader-profile-card h-100">
                                            <div class="leader-photo">
                                                @if (
                                                    $representative?->photo &&
                                                        file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $representative->photo)))
                                                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $representative->photo) }}"
                                                        alt="{{ $representative->name ?? $leader->username }}"
                                                        class="leader-photo-image" loading="lazy">
                                                @else
                                                    <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                        alt="Default profile picture" class="leader-photo-image"
                                                        loading="lazy">
                                                @endif
                                            </div>

                                            <div class="leader-profile-details">
                                                <h6 class="leader-name">
                                                    {{-- {{ $representative?->name ?? $leader->username }} --}}
                                                    {{ $representative?->memberPersonalProfile?->first_zz }}
                                                </h6>

                                                <div class="leader-position">
                                                    {{ $leader->position?->name ?? 'Student Representative' }}
                                                </div>

                                                @if ($leader->remarks)
                                                    <p class="leader-remarks">
                                                        {{ $leader->remarks }}
                                                    </p>
                                                @endif

                                                @if ($socialLinks->isNotEmpty())
                                                    <div class="leader-social-links">
                                                        @foreach ($socialLinks as $social)
                                                            @php
                                                                $platform = $social->thePlatform;
                                                            @endphp

                                                            <a href="{{ $social->link }}" target="_blank"
                                                                rel="noopener noreferrer" class="leader-social-link"
                                                                title="{{ $platform?->name ?? $social->platform }}"
                                                                aria-label="{{ $platform?->name ?? $social->platform }}">

                                                                @if (
                                                                    $platform?->logo &&
                                                                        file_exists(public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo)))
                                                                    <img src="{{ asset('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo) }}"
                                                                        alt="{{ $platform->name }}" loading="lazy">
                                                                @else
                                                                    <i class="fa fa-globe" aria-hidden="true"></i>
                                                                @endif
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="leadership-empty-state">
                            <i class="fa fa-users fa-2x mb-3"></i>

                            <h6>No Student Leaders Found</h6>

                            <p class="mb-0">
                                There are no student representatives for this academic session.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- ASSOCIATION ADVISORS --}}
                @if ($data['advisors']->isNotEmpty())
                    <div class="leadership-section advisors-section">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="leadership-section-icon advisor-icon">
                                <i class="fa fa-user-tie"></i>
                            </div>

                            <div>
                                <h4 class="mb-1">Guidance & support</h4>
                                <p class="text-muted mb-0">
                                    The advisers and representatives who provide guidance, institutional support, and
                                    direction to the association.
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            @forelse ($data['advisors'] as $advisor)
                                @php
                                    $advisorPhoto = $advisor->photo
                                        ? 'assets/tenant_' .
                                            tenant()->id .
                                            '/uploads/leadership/advisor/' .
                                            $advisor->photo
                                        : null;

                                    $hasAdvisorPhoto = $advisorPhoto && file_exists(public_path($advisorPhoto));
                                @endphp

                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                    <div class="advisor-profile-card h-100">
                                        <div class="advisor-photo">
                                            @if ($hasAdvisorPhoto)
                                                <img src="{{ asset($advisorPhoto) }}" alt="{{ $advisor->name }}"
                                                    class="advisor-photo-image" loading="lazy">
                                            @else
                                                <img src="{{ asset('system/images/default_profile_pic.webp') }}"
                                                    alt="{{ $advisor->name }}" class="advisor-photo-image"
                                                    loading="lazy">
                                            @endif
                                        </div>

                                        <div class="advisor-details">
                                            <h6>{{ $advisor->name }}</h6>

                                            @if ($advisor->position)
                                                <div class="advisor-position">
                                                    {{ $advisor->position }}
                                                </div>
                                            @endif

                                            @if ($advisor->remarks)
                                                <p class="advisor-remarks">
                                                    {{ $advisor->remarks }}
                                                </p>
                                            @endif

                                            <div class="advisor-service-period">
                                                <i class="fa fa-calendar-alt me-1"></i>
                                                {{ $advisor->tenure?->start_date?->format('F Y') ?? 'N/A' }}
                                                –
                                                {{ $advisor->tenure?->end_date?->format('F Y') ?? 'Present' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="leadership-empty-state">
                                        <i class="fa fa-user-tie fa-2x mb-3"></i>

                                        <h6>No Advisors Found</h6>

                                        <p class="mb-0">
                                            No advisors were found for this academic session.
                                        </p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </section>

    {{-- CONTACT --}}
    <section class="mx-section mx-section-light">
        <div class="container">

            <div class="mx-section-heading text-center">
                <span class="mx-kicker">Get In Touch</span>

                <h2>Contact The Association</h2>

                <p>
                    Have a question, suggestion, or enquiry? Reach out using the
                    available contact information.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="mx-contact-card">
                        <div class="mx-contact-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>

                        <div>
                            <h3>Email Address</h3>

                            @if ($contactEmail)
                                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                            @else
                                <p>Email information will be provided here.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="mx-contact-card">
                        <div class="mx-contact-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>

                        <div>
                            <h3>Phone Number</h3>

                            @if ($contactPhone)
                                <a href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
                            @else
                                <p>Phone information will be provided here.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="mx-contact-card">
                        <div class="mx-contact-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div>
                            <h3>Office / Address</h3>

                            @if ($contactAddress)
                                <p>{{ $contactAddress }}</p>
                            @else
                                <p>Association address will be provided here.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-section mx-section-white">
        <div class="container">
            <div class="mx-cta">
                <span class="mx-kicker">Get Involved</span>

                <h2>Our Community. Our Future.</h2>

                <p>
                    Stay informed, participate in association initiatives, and contribute
                    to building a stronger and more connected community.
                </p>

                <div class="mx-actions">
                    <a href="{{ route('homepage') }}" class="mx-button">
                        <i class="fa-solid fa-house"></i>
                        Back to Home
                    </a>

                    @auth
                        <a href="{{ route('homepage') }}" class="mx-button mx-button-outline">
                            <i class="fa-solid fa-arrow-right"></i>
                            Continue to Portal
                        </a>
                    @else
                        <a href="{{ route('loginPage') }}" class="mx-button mx-button-outline">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Member Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

</main>

@endsection
