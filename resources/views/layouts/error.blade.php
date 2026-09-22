<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Something went wrong') | Diva House Beauty</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500;1,600&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #26231f; --paper: #fbf9f5; --clay: #d8785e; --sage: #a9b9a4; --line: #ded9d0; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; color: var(--ink); background: var(--paper); font-family: 'DM Sans', sans-serif; }
        .page { min-height: 100vh; padding: 28px clamp(24px, 6vw, 88px) 42px; display: flex; flex-direction: column; overflow: hidden; position: relative; }
        .brand { display: inline-flex; align-items: center; gap: 10px; width: fit-content; color: inherit; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .brand-mark { width: 24px; height: 24px; border: 1.5px solid var(--clay); border-radius: 50% 50% 50% 6px; transform: rotate(-25deg); }
        .content { flex: 1; width: min(100%, 1080px); margin: 0 auto; display: grid; grid-template-columns: minmax(0, 1fr) minmax(280px, .85fr); align-items: center; gap: clamp(36px, 8vw, 120px); padding: 56px 0 30px; }
        .eyebrow { margin: 0 0 20px; color: #746e65; font: 500 11px/1 'DM Mono', monospace; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 0; max-width: 660px; font: 600 clamp(42px, 6vw, 78px)/.98 'Playfair Display', serif; letter-spacing: -.045em; }
        .copy { margin: 24px 0 32px; max-width: 480px; color: #625c54; font-size: 16px; line-height: 1.65; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 46px; padding: 0 21px; border: 1px solid var(--ink); color: var(--ink); background: transparent; font: 600 12px/1 'DM Sans', sans-serif; letter-spacing: .04em; text-decoration: none; cursor: pointer; transition: background .2s, color .2s, transform .2s; }
        .button:hover { background: var(--ink); color: var(--paper); transform: translateY(-2px); }
        .button.primary { border-color: var(--clay); background: var(--clay); color: #fff; }
        .button.primary:hover { background: #bd644e; border-color: #bd644e; }
        .art { min-height: 330px; position: relative; display: grid; place-items: center; }
        .disc { width: min(31vw, 315px); aspect-ratio: 1; border-radius: 50%; background: var(--sage); position: relative; }
        .disc::before { content: ''; position: absolute; width: 43%; aspect-ratio: 1; right: -16%; top: 10%; border-radius: 50%; background: var(--clay); }
        .disc::after { content: ''; position: absolute; width: 75%; aspect-ratio: .8; left: -24%; bottom: -17%; border: 1px solid var(--ink); border-radius: 50% 50% 0 0; transform: rotate(-14deg); }
        .status { position: absolute; z-index: 1; color: var(--ink); font: 500 clamp(70px, 10vw, 132px)/1 'DM Mono', monospace; letter-spacing: -.1em; transform: translate(-11%, 4%); }
        @media (max-width: 700px) { .page { padding-top: 22px; } .content { grid-template-columns: 1fr; padding-top: 58px; gap: 6px; } .art { min-height: 210px; order: -1; justify-content: end; } .disc { width: 190px; } .status { font-size: 82px; } h1 { font-size: clamp(42px, 13vw, 60px); } .copy { margin-top: 18px; } }
    </style>
</head>
<body>
    <main class="page">
        <a class="brand" href="{{ route('home') }}" aria-label="Diva House Beauty home"><span class="brand-mark" aria-hidden="true"></span>Diva House Beauty</a>
        <section class="content" aria-labelledby="error-title">
            <div>
                <p class="eyebrow">@yield('eyebrow', 'A small detour')</p>
                <h1 id="error-title">@yield('heading')</h1>
                <p class="copy">@yield('message')</p>
                <div class="actions">@yield('actions')</div>
            </div>
            <div class="art" aria-hidden="true"><span class="status">@yield('code')</span><span class="disc"></span></div>
        </section>
    </main>
</body>
</html>
