<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:system-ui,sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem}
        .card{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:3rem 2.5rem;max-width:480px;width:100%;text-align:center}
        .code{font-size:72px;font-weight:700;color:#6c757d;line-height:1;margin-bottom:.5rem}
        h1{font-size:22px;font-weight:600;color:#1a1a18;margin-bottom:.75rem}
        p{font-size:15px;color:#6c757d;line-height:1.7;margin-bottom:2rem}
        .btn{display:inline-block;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;margin:0 4px}
        .btn-primary{background:#0d6efd;color:#fff}
        .btn-outline{background:transparent;color:#6c757d;border:1px solid #dee2e6}
        .btn:hover{opacity:.85}
    </style>
</head>
<body>
    <div class="card">
        <div style="font-size:64px;margin-bottom:1rem;">🔍</div>
        <div class="code">404</div>
        <h1>Page Not Found</h1>
        <p>
            The page you are looking for does not exist, may have been moved,
            or the link you followed might be broken.
        </p>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline">← Go Back</a>
            <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
        </div>
    </div>
</body>
</html>
