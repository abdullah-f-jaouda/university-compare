<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'مقارنة الجامعات') — بنغازي</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: #f6f7f9; }
        .table-fit td, .table-fit th { vertical-align: middle; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">مقارنة الجامعات — بنغازي</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="القائمة">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('universities.index') }}">الجامعات</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('programs.index') }}">البرامج</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="alert alert-warning mb-0 rounded-0 text-center small">
        ⚠ أسماء الجامعات حقيقية، لكن الرسوم والتفاصيل المعروضة <strong>بيانات تجريبية</strong> لأغراض العرض والاختبار، وليست معلومات رسمية.
        النتائج والتقديرات استرشادية فقط.
    </div>

    <main class="container py-4">
        @yield('content')
    </main>

    <footer class="text-center text-muted small py-4">
        نظام مقارنة الجامعات ودعم القرار — مشروع تخرج
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
