<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>404 | eBarangayHub</title>

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
  max-width: 520px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
}
.code {
  display: inline-flex;
  border: 1px solid #99f6e4;
  border-radius: 6px;
  background: #f0fdfa;
  padding: .35rem .65rem;
  color: #0f766e;
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
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background: #ffffff;
  padding: .65rem .9rem;
  color: #334155;
  font-size: .9rem;
  font-weight: 700;
  text-decoration: none;
}
.btn.primary {
  border-color: #0f766e;
  background: #0f766e;
  color: #ffffff;
}
.hint {
  margin-top: 1rem;
  border-top: 1px dashed #cbd5e1;
  padding-top: .85rem;
  color: #64748b;
  font-size: .8rem;
}
</style>
</head>

<body>
<main class="card" role="main">
  <div class="code">404 - Page Not Found</div>
  <h1><?= html_escape($heading) ?></h1>
  <p><?= html_escape($message) ?></p>

  <div class="actions">
    <a class="btn primary" href="/">Back to Home</a>
    <a class="btn" href="javascript:history.back()">Go Back</a>
  </div>

  <div class="hint">
    eBarangayHub could not find this page. Check the URL or return to the main portal.
  </div>
</main>
</body>
</html>
