<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Babiauto · Votre trajet, à votre rythme</title>
    <meta name="description" content="Babiauto — l'application de VTC à Abidjan. Commandez une course, suivez votre chauffeur en temps réel, payez en espèces, Mobile Money ou carte.">
    <link rel="icon" type="image/png" href="{{ asset('images/babiauto-logo-transparent.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --orange: #F39423;
            --orange-deep: #E67A0C;
            --orange-soft: #FDE5C7;
            --ink: #1A1612;
            --ink-2: #3D362E;
            --ink-3: #6B6358;
            --paper: #FFFBF5;
            --paper-2: #F5EFE5;
            --line: rgba(26, 22, 18, 0.08);
            --line-2: rgba(26, 22, 18, 0.14);
            --green: oklch(0.68 0.13 145);
            --lagoon: oklch(0.68 0.09 220);
            --shadow-card: 0 1px 0 rgba(255,255,255,0.8) inset, 0 8px 24px rgba(26,22,18,0.10), 0 1px 2px rgba(26,22,18,0.06);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Manrope', ui-sans-serif, system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: var(--paper);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        .numeric { font-family: 'Space Grotesk', 'Manrope', sans-serif; font-variant-numeric: tabular-nums; }
        a { color: inherit; text-decoration: none; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 24px; }

        /* ── Nav ── */
        .nav {
            position: sticky; top: 0; z-index: 50;
            backdrop-filter: blur(12px);
            background: rgba(26, 20, 16, 0.72);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand img { height: 34px; width: auto; }
        .brand span { color: #fff; font-weight: 800; font-size: 18px; letter-spacing: -0.01em; }
        .nav-links { display: flex; align-items: center; gap: 28px; }
        .nav-links a { color: rgba(255,255,255,0.72); font-weight: 600; font-size: 14px; transition: color 140ms ease; }
        .nav-links a:hover { color: #fff; }
        .nav-cta {
            background: var(--orange); color: #fff !important; padding: 9px 18px;
            border-radius: 999px; font-size: 14px; box-shadow: 0 6px 16px rgba(243,148,35,0.3);
            transition: background 140ms ease, transform 80ms ease;
        }
        .nav-cta:hover { background: var(--orange-deep); }
        @media (max-width: 720px) { .nav-links a:not(.nav-cta) { display: none; } }

        /* ── Hero ── */
        .hero {
            position: relative; overflow: hidden;
            background: radial-gradient(120% 90% at 50% -10%, #2A211A 0%, #1A1410 55%, #0F0C0A 100%);
            color: #fff; text-align: center;
            padding: 88px 0 120px;
        }
        .hero .glow {
            position: absolute; top: 26%; left: 50%; transform: translate(-50%, -50%);
            width: 760px; height: 760px; border-radius: 50%; pointer-events: none;
            background: radial-gradient(circle, rgba(243,148,35,0.34) 0%, rgba(243,148,35,0.12) 36%, rgba(243,148,35,0) 66%);
        }
        .hero .dots { position: absolute; inset: 0; opacity: 0.06; pointer-events: none; }
        .hero-inner { position: relative; z-index: 2; }
        .pill {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            border: 1px solid rgba(255,255,255,0.14); border-radius: 999px;
            padding: 7px 14px; margin-bottom: 30px;
        }
        .pill .dot { width: 6px; height: 6px; border-radius: 999px; background: var(--orange); box-shadow: 0 0 8px var(--orange); }
        .hero img.logo {
            width: 230px; max-width: 70vw; height: auto; display: block; margin: 0 auto 26px;
            filter: drop-shadow(0 18px 40px rgba(243,148,35,0.40)) drop-shadow(0 4px 12px rgba(0,0,0,0.35));
            animation: float 3.6s cubic-bezier(0.4,0,0.2,1) infinite;
        }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-7px); } }
        .hero h1 {
            font-size: clamp(34px, 6vw, 60px); font-weight: 800; letter-spacing: -0.025em;
            line-height: 1.05; margin-bottom: 18px;
        }
        .hero h1 .accent { color: var(--orange); }
        .hero p.sub {
            font-size: clamp(16px, 2.2vw, 20px); color: rgba(255,255,255,0.74);
            max-width: 540px; margin: 0 auto 36px; font-weight: 500;
        }
        .hero-ctas { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            height: 54px; padding: 0 28px; border-radius: 14px;
            font-weight: 700; font-size: 16px; cursor: pointer;
            transition: transform 80ms ease, background 140ms ease, box-shadow 140ms ease;
        }
        .btn:active { transform: scale(0.985); }
        .btn-primary { background: var(--orange); color: #fff; box-shadow: 0 10px 28px rgba(243,148,35,0.36); }
        .btn-primary:hover { background: var(--orange-deep); }
        .btn-ghost { background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.16); }
        .btn-ghost:hover { background: rgba(255,255,255,0.14); }

        /* ── Stats strip ── */
        .stats {
            position: relative; z-index: 3; margin-top: -56px;
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
        }
        .stat {
            background: var(--paper); border: 1px solid var(--line); border-radius: 20px;
            padding: 24px; text-align: center; box-shadow: var(--shadow-card);
        }
        .stat .n { font-size: 40px; font-weight: 700; letter-spacing: -0.02em; color: var(--ink); }
        .stat .l { font-size: 13px; font-weight: 600; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }
        @media (max-width: 640px) { .stats { grid-template-columns: 1fr; } .stat .n { font-size: 34px; } }

        /* ── Section shell ── */
        section.block { padding: 88px 0; }
        .eyebrow { color: var(--orange-deep); font-weight: 800; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; }
        .section-title { font-size: clamp(26px, 4vw, 38px); font-weight: 800; letter-spacing: -0.02em; margin: 10px 0 12px; }
        .section-sub { color: var(--ink-3); font-size: 17px; max-width: 560px; }
        .center { text-align: center; margin: 0 auto; }
        .center .section-sub { margin-left: auto; margin-right: auto; }

        /* ── Vehicle classes ── */
        .vehicles { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-top: 48px; }
        .vcard {
            background: var(--paper); border: 1px solid var(--line); border-radius: 22px;
            padding: 26px 22px; box-shadow: var(--shadow-card);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }
        .vcard:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(26,22,18,0.14); }
        .vart {
            width: 84px; height: 58px; border-radius: 14px; background: var(--paper-2);
            display: flex; align-items: center; justify-content: center; margin-bottom: 18px;
        }
        .vcard h3 { font-size: 19px; font-weight: 800; }
        .vcard .desc { color: var(--ink-3); font-size: 14px; font-weight: 600; margin-top: 2px; }
        .vmeta { display: flex; gap: 8px; margin: 16px 0; }
        .tag {
            font-size: 12px; font-weight: 700; color: var(--ink-2);
            background: rgba(26,22,18,0.05); border-radius: 8px; padding: 4px 9px;
        }
        .vprice { display: flex; align-items: baseline; gap: 6px; border-top: 1px solid var(--line); padding-top: 16px; }
        .vprice .from { font-size: 12px; color: var(--ink-3); font-weight: 600; }
        .vprice .amt { font-size: 24px; font-weight: 800; }
        .vprice .cur { font-size: 13px; font-weight: 700; color: var(--ink-3); }
        @media (max-width: 900px) { .vehicles { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .vehicles { grid-template-columns: 1fr; } }

        /* ── Features ── */
        .features-block { background: var(--paper-2); }
        .features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-top: 48px; }
        .feature {
            background: var(--paper); border: 1px solid var(--line); border-radius: 20px; padding: 28px;
        }
        .feature .ic {
            width: 46px; height: 46px; border-radius: 13px; background: var(--orange-soft);
            display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: var(--orange-deep);
        }
        .feature h3 { font-size: 17px; font-weight: 800; margin-bottom: 6px; }
        .feature p { color: var(--ink-3); font-size: 14.5px; }
        @media (max-width: 900px) { .features { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px) { .features { grid-template-columns: 1fr; } }

        /* ── Steps ── */
        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-top: 48px; }
        .step { padding: 8px 4px; }
        .step .num {
            width: 40px; height: 40px; border-radius: 999px; background: var(--ink); color: #fff;
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;
            margin-bottom: 16px;
        }
        .step h3 { font-size: 16px; font-weight: 800; margin-bottom: 4px; }
        .step p { color: var(--ink-3); font-size: 14px; }
        @media (max-width: 760px) { .steps { grid-template-columns: repeat(2, 1fr); } }

        /* ── API ── */
        .api-block { background: radial-gradient(120% 100% at 50% 0%, #241C16 0%, #15110D 100%); color: #fff; }
        .api-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
        .api-block .eyebrow { color: var(--orange); }
        .api-block .section-sub { color: rgba(255,255,255,0.7); }
        .api-block h2 { color: #fff; }
        .endpoints {
            background: rgba(0,0,0,0.32); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px;
            padding: 8px; font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 13.5px;
        }
        .ep { display: flex; align-items: center; gap: 12px; padding: 13px 14px; border-radius: 12px; }
        .ep + .ep { border-top: 1px solid rgba(255,255,255,0.06); }
        .verb { font-weight: 700; font-size: 11px; padding: 3px 8px; border-radius: 6px; letter-spacing: 0.04em; }
        .verb.get { background: rgba(104,180,140,0.22); color: #8fe3b8; }
        .verb.post { background: rgba(243,148,35,0.22); color: var(--orange); }
        .ep .path { color: rgba(255,255,255,0.9); }
        @media (max-width: 800px) { .api-grid { grid-template-columns: 1fr; gap: 28px; } }

        /* ── Footer ── */
        footer { background: #0F0C0A; color: rgba(255,255,255,0.6); padding: 44px 0; }
        .foot-inner { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .foot-inner .brand span { font-size: 16px; }
        footer .meta { font-size: 13px; }
    </style>
</head>
<body>
@php
    $carts = [
        'moto' => '<svg viewBox="0 0 64 44" width="56" height="40" fill="none"><circle cx="14" cy="32" r="6" stroke="#F39423" stroke-width="2"/><circle cx="50" cy="32" r="6" stroke="#F39423" stroke-width="2"/><path d="M 20 32 L 36 18 L 50 32 M 36 18 L 44 14 M 28 18 L 36 18" stroke="#F39423" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'xl' => '<svg viewBox="0 0 64 44" width="62" height="42" fill="none"><path d="M 4 30 L 4 22 L 12 14 L 50 14 L 58 22 L 60 30" stroke="#F39423" stroke-width="2" stroke-linejoin="round"/><path d="M 4 30 L 60 30" stroke="#F39423" stroke-width="2"/><circle cx="16" cy="32" r="5" stroke="#F39423" stroke-width="2" fill="#fff"/><circle cx="46" cy="32" r="5" stroke="#F39423" stroke-width="2" fill="#fff"/><path d="M 14 14 L 14 22 M 26 14 L 26 22 M 38 14 L 38 22 M 50 14 L 50 22" stroke="#F39423" stroke-width="1"/></svg>',
        'confort' => '<svg viewBox="0 0 64 44" width="62" height="42" fill="none"><path d="M 4 30 L 8 22 L 18 18 L 24 14 L 42 14 L 50 22 L 60 30" stroke="#F39423" stroke-width="2" stroke-linejoin="round"/><path d="M 4 30 L 60 30" stroke="#F39423" stroke-width="2"/><circle cx="16" cy="32" r="5" stroke="#F39423" stroke-width="2" fill="#fff"/><circle cx="46" cy="32" r="5" stroke="#F39423" stroke-width="2" fill="#fff"/><path d="M 22 18 L 32 14 M 32 14 L 32 22 M 32 22 L 22 22" stroke="#F39423" stroke-width="1"/></svg>',
        'mini' => '<svg viewBox="0 0 64 44" width="56" height="40" fill="none"><path d="M 6 30 L 10 22 L 22 18 L 42 18 L 50 22 L 56 30" stroke="#F39423" stroke-width="2" stroke-linejoin="round"/><path d="M 6 30 L 56 30" stroke="#F39423" stroke-width="2"/><circle cx="16" cy="32" r="4.5" stroke="#F39423" stroke-width="2" fill="#fff"/><circle cx="44" cy="32" r="4.5" stroke="#F39423" stroke-width="2" fill="#fff"/></svg>',
    ];
@endphp

    <nav class="nav">
        <div class="wrap nav-inner">
            <a class="brand" href="#top">
                <img src="{{ asset('images/babiauto-logo-transparent.png') }}" alt="Babiauto">
                <span>Babiauto</span>
            </a>
            <div class="nav-links">
                <a href="#tarifs">Tarifs</a>
                <a href="#fonctionnalites">Fonctionnalités</a>
                <a href="#api">API</a>
                <a class="nav-cta" href="#tarifs">Réserver</a>
            </div>
        </div>
    </nav>

    <header class="hero" id="top">
        <div class="glow"></div>
        <svg class="dots" width="100%" height="100%">
            <defs>
                <pattern id="d" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                    <circle cx="1" cy="1" r="1" fill="#fff"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#d)"/>
        </svg>
        <div class="wrap hero-inner">
            <span class="pill"><span class="dot"></span> v1.0 · Abidjan 🇨🇮</span>
            <img class="logo" src="{{ asset('images/babiauto-logo-transparent.png') }}" alt="Babiauto">
            <h1>Votre trajet,<br><span class="accent">à votre rythme.</span></h1>
            <p class="sub">Commandez une course à Abidjan en quelques secondes. Suivez votre chauffeur en temps réel, payez comme vous voulez.</p>
            <div class="hero-ctas">
                <a class="btn btn-primary" href="#tarifs">Réserver une course →</a>
                <a class="btn btn-ghost" href="#api">Explorer l'API</a>
            </div>
        </div>
    </header>

    <div class="wrap">
        <div class="stats">
            <div class="stat">
                <div class="n numeric">{{ number_format($stats['drivers'], 0, ',', ' ') }}</div>
                <div class="l">Chauffeurs</div>
            </div>
            <div class="stat">
                <div class="n numeric">{{ $stats['vehicle_classes'] }}</div>
                <div class="l">Catégories</div>
            </div>
            <div class="stat">
                <div class="n numeric">{{ $stats['neighborhoods'] }}+</div>
                <div class="l">Quartiers couverts</div>
            </div>
        </div>
    </div>

    <section class="block" id="tarifs">
        <div class="wrap">
            <div class="center" style="max-width:620px;">
                <span class="eyebrow">Nos véhicules</span>
                <h2 class="section-title">Une course pour chaque besoin</h2>
                <p class="section-sub">Du deux-roues le plus rapide au SUV familial — des tarifs clairs en francs CFA, sans surprise.</p>
            </div>
            <div class="vehicles">
                @foreach ($vehicleClasses as $class)
                    <div class="vcard">
                        <div class="vart">{!! $carts[$class->slug] ?? $carts['mini'] !!}</div>
                        <h3>{{ $class->name }}</h3>
                        <div class="desc">{{ $class->description_fr }}</div>
                        <div class="vmeta">
                            <span class="tag">{{ $class->seats }} {{ $class->seats > 1 ? 'places' : 'place' }}</span>
                            <span class="tag numeric">~{{ $class->default_eta_minutes }} min</span>
                        </div>
                        <div class="vprice">
                            <span class="from">dès</span>
                            <span class="amt numeric">{{ number_format($class->min_fare, 0, ',', ' ') }}</span>
                            <span class="cur">F CFA</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="block features-block" id="fonctionnalites">
        <div class="wrap">
            <div class="center" style="max-width:620px;">
                <span class="eyebrow">Pourquoi Babiauto</span>
                <h2 class="section-title">Pensé pour Abidjan</h2>
                <p class="section-sub">Tout ce qu'il faut pour voyager sereinement, du Plateau à Cocody.</p>
            </div>
            <div class="features">
                <div class="feature">
                    <div class="ic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-7.5 7-13a7 7 0 1 0-14 0c0 5.5 7 13 7 13z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    </div>
                    <h3>Suivi en temps réel</h3>
                    <p>Localisez votre chauffeur sur la carte à chaque instant, de la prise en charge à l'arrivée.</p>
                </div>
                <div class="feature">
                    <div class="ic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><circle cx="12" cy="12.5" r="3"/></svg>
                    </div>
                    <h3>Espèces, Mobile Money ou carte</h3>
                    <p>Payez en F CFA comme vous le souhaitez — Orange Money, Wave, cash ou carte bancaire.</p>
                </div>
                <div class="feature">
                    <div class="ic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.6 5.5 6 .9-4.3 4.2 1 6L12 16.8l-5.3 2.8 1-6L3.4 9.4l6-.9L12 3z"/></svg>
                    </div>
                    <h3>Chauffeurs notés</h3>
                    <p>Des chauffeurs vérifiés et évalués après chaque course. Laissez un pourboire en un geste.</p>
                </div>
                <div class="feature">
                    <div class="ic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7v9a2 2 0 0 1-2 2h-4v-6h-6v6H5a2 2 0 0 1-2-2v-9z"/></svg>
                    </div>
                    <h3>Lieux enregistrés</h3>
                    <p>Maison, bureau, vos adresses favorites — commandez en un seul tap.</p>
                </div>
                <div class="feature">
                    <div class="ic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M2 12h20M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg>
                    </div>
                    <h3>Français & English</h3>
                    <p>Une expérience bilingue, adaptée à tous les voyageurs de la lagune.</p>
                </div>
                <div class="feature">
                    <div class="ic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Sécurité intégrée</h3>
                    <p>Partage de trajet, bouton sécurité et détails du véhicule avant de monter.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="block">
        <div class="wrap">
            <div class="center" style="max-width:620px;">
                <span class="eyebrow">Comment ça marche</span>
                <h2 class="section-title">Quatre étapes, c'est tout</h2>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="num">1</div>
                    <h3>Où allez-vous&nbsp;?</h3>
                    <p>Saisissez votre destination ou choisissez un lieu enregistré.</p>
                </div>
                <div class="step">
                    <div class="num">2</div>
                    <h3>Choisissez</h3>
                    <p>Comparez Babi Mini, Confort, XL ou Moto et leur tarif.</p>
                </div>
                <div class="step">
                    <div class="num">3</div>
                    <h3>On arrive</h3>
                    <p>Le chauffeur le plus proche vous rejoint en quelques minutes.</p>
                </div>
                <div class="step">
                    <div class="num">4</div>
                    <h3>En route</h3>
                    <p>Profitez du trajet, payez et notez votre chauffeur.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="block api-block" id="api">
        <div class="wrap">
            <div class="api-grid">
                <div>
                    <span class="eyebrow">Pour les développeurs</span>
                    <h2 class="section-title">Propulsé par l'API Babiauto</h2>
                    <p class="section-sub">Une API REST complète (Laravel · MySQL · Redis) pour réserver, suivre et facturer les courses. Authentification par jeton Bearer.</p>
                    <p style="margin-top:22px;">
                        <a class="btn btn-primary" href="/up">État du service →</a>
                    </p>
                </div>
                <div class="endpoints">
                    <div class="ep"><span class="verb get">GET</span><span class="path">/api/v1/vehicle-classes</span></div>
                    <div class="ep"><span class="verb get">GET</span><span class="path">/api/v1/places?q=cocody</span></div>
                    <div class="ep"><span class="verb post">POST</span><span class="path">/api/v1/rides/estimate</span></div>
                    <div class="ep"><span class="verb post">POST</span><span class="path">/api/v1/rides</span></div>
                    <div class="ep"><span class="verb get">GET</span><span class="path">/api/v1/rides/{id}/tracking</span></div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="wrap foot-inner">
            <a class="brand" href="#top">
                <img src="{{ asset('images/babiauto-logo-transparent.png') }}" alt="Babiauto" style="height:30px;">
                <span style="color:#fff;font-weight:800;">Babiauto</span>
            </a>
            <div class="meta">© {{ date('Y') }} Babiauto · Abidjan, Côte d'Ivoire · Votre trajet, à votre rythme.</div>
        </div>
    </footer>
</body>
</html>
