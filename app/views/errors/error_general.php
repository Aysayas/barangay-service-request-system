<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error | eBarangayHub</title>
    <style nonce="<?= defined('CSP_NONCE') ? CSP_NONCE : '' ?>">
        * { box-sizing: border-box; }
        html, body { min-height: 100%; margin: 0; }
        body {
            display: grid;
            place-items: center;
            padding: 1rem;
            background: linear-gradient(180deg, #f8fafc 0%, #eef7f6 100%);
            color: #0f172a;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .card {
            width: 100%;
            max-width: 540px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 1.5rem;
            box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
        }
        .eyebrow {
            display: inline-flex;
            border: 1px solid #fecdd3;
            border-radius: 6px;
            background: #fff1f2;
            padding: .35rem .65rem;
            color: #be123c;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        h1 {
            margin: .9rem 0 .45rem;
            font-size: clamp(1.65rem, 4vw, 2.25rem);
            line-height: 1.15;
        }
        p {
            margin: 0;
            color: #475569;
            font-size: .95rem;
            line-height: 1.65;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            margin-top: 1.25rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #0f766e;
            border-radius: 6px;
            background: #0f766e;
            padding: .65rem .9rem;
            color: #ffffff;
            font-size: .9rem;
            font-weight: 700;
            text-decoration: none;
        }
        @media (max-width: 480px) {
            body { align-items: start; padding: .75rem; }
            .card { padding: 1rem; }
            .actions { flex-direction: column; }
            .btn { width: 100%; min-height: 44px; }
        }
    </style>
</head>
<body>
    <main class="card" role="main">
        <span class="eyebrow">System Notice</span>
        <h1><?= htmlspecialchars((string) $heading, ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="actions">
            <a class="btn" href="/">Back to eBarangayHub</a>
        </div>
    </main>
</body>
</html>
