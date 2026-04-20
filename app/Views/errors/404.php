<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Bulunamadı - 404</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen font-[Inter]">
    <div class="text-center">
        <h1 class="text-9xl font-extrabold text-gray-200">404</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mt-4">Aradığınız sayfa bulunamadı.</h2>
        <p class="text-gray-500 mt-2 mb-8">Bu sayfa taşınmış, silinmiş veya hiç var olmamış olabilir.</p>
        <a href="<?= BASE_URL ?? '/' ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Ana Sayfaya Dön
        </a>
    </div>
</body>
</html>
