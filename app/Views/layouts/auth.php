<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'EkaYazılım SaaS' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-blue-600">EkaYazılım</h1>
                <h2 class="mt-4 text-2xl font-bold text-gray-900"><?= $title ?? 'Hoş Geldiniz' ?></h2>
            </div>
            
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow-xl shadow-gray-200/50 sm:rounded-xl sm:px-10 border border-gray-100">
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm border border-red-100">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="bg-green-50 text-green-600 p-3 rounded-lg mb-6 text-sm border border-green-100">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?= $content ?? '' ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
