<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') | Diva House Beauty</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #24211e; --paper: #faf8f4; --line: #dfdad2; --accent: @yield('accent', '#bd674f'); }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: var(--ink); background: var(--paper); font-family: 'DM Sans', sans-serif; }
        .shell { width: min(100% - 40px, 980px); min-height: 100vh; margin: 0 auto; padding: 28px 0 50px; }
        .brand { display: inline-block; color: inherit; font-size: 12px; font-weight: 700; letter-spacing: .09em; text-decoration: none; text-transform: uppercase; }
        .brand-mark { display: inline-block; width: 19px; height: 19px; margin-right: 8px; vertical-align: -4px; border: 1.5px solid var(--accent); border-radius: 50% 50% 50% 5px; transform: rotate(-25deg); }
        .result { max-width: 650px; margin: clamp(64px, 13vh, 132px) auto 0; }
        .eyebrow { margin: 0 0 19px; color: #777068; font-size: 11px; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
        h1 { max-width: 580px; margin: 0; font: 600 clamp(38px, 6vw, 62px)/1 'Playfair Display', serif; letter-spacing: -.045em; }
        .intro { max-width: 510px; margin: 22px 0 32px; color: #625c54; font-size: 16px; line-height: 1.65; }
        .details { margin: 0 0 30px; border-top: 1px solid var(--line); }
        .detail { display: flex; align-items: baseline; justify-content: space-between; gap: 24px; padding: 16px 0; border-bottom: 1px solid var(--line); }
        .detail dt { color: #7c756e; font-size: 12px; }
        .detail dd { margin: 0; color: var(--ink); font-size: 13px; font-weight: 600; text-align: right; overflow-wrap: anywhere; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .action { display: inline-flex; align-items: center; justify-content: center; min-height: 45px; padding: 0 19px; border: 1px solid var(--ink); background: transparent; color: var(--ink); font: 600 12px/1 'DM Sans', sans-serif; letter-spacing: .03em; text-decoration: none; cursor: pointer; transition: background .2s, color .2s; }
        .action:hover { background: var(--ink); color: #fff; }
        .action.primary { border-color: var(--accent); background: var(--accent); color: #fff; }
        .action.primary:hover { filter: brightness(.91); }
        .note { max-width: 510px; margin: 25px 0 0; color: #857e76; font-size: 12px; line-height: 1.6; }
        @media (max-width: 640px) { .shell { width: min(100% - 32px, 980px); padding-top: 22px; } .result { margin-top: 75px; } .detail { align-items: flex-start; flex-direction: column; gap: 5px; } .detail dd { text-align: left; } }
    </style>
</head>
<body>
    <main class="shell">
        <a class="brand" href="{{ route('home') }}" aria-label="Diva House Beauty home"><span class="brand-mark" aria-hidden="true"></span>Diva House Beauty</a>
        <section class="result" aria-labelledby="result-title">
            <p class="eyebrow">@yield('eyebrow')</p>
            <h1 id="result-title">@yield('heading')</h1>
            <p class="intro">@yield('message')</p>
            @yield('details')
            <div class="actions">@yield('actions')</div>
            @hasSection('note')<p class="note">@yield('note')</p>@endif
        </section>
    </main>
</body>
</html>
