<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title><?= htmlspecialchars($title ?? 'Panel') ?> | Eka Developer Cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="overflow-hidden bg-slate-50 font-sans text-slate-800 antialiased">
    <div id="toast-container" class="pointer-events-none fixed right-4 top-4 z-50 space-y-3 sm:right-5 sm:top-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="pointer-events-auto flex max-w-sm items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-xl">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><i class="fa-solid fa-check"></i></div>
                <div><p class="text-sm font-bold">Başarılı</p><p class="mt-0.5 text-xs"><?= htmlspecialchars($_SESSION['success']) ?></p></div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="pointer-events-auto flex max-w-sm items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow-xl">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div><p class="text-sm font-bold">Hata</p><p class="mt-0.5 text-xs"><?= htmlspecialchars($_SESSION['error']) ?></p></div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <?php
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $menuItems = [
        ['path' => '/dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Genel Bakış'],
        ['path' => '/projects', 'icon' => 'fa-diagram-project', 'label' => 'Projeler'],
        ['path' => '/uygulamalar', 'icon' => 'fa-cubes', 'label' => 'Uygulamalar'],
        ['path' => '/users', 'icon' => 'fa-users', 'label' => 'Ekip'],
        ['path' => '/api-keys', 'icon' => 'fa-key', 'label' => 'API Anahtarları'],
        ['path' => '/notifications', 'icon' => 'fa-bell', 'label' => 'Bildirimler'],
        ['path' => '/billing', 'icon' => 'fa-credit-card', 'label' => 'Paket & Fatura'],
    ];
    $kullanici = Core\EkaAuth::user();
    ?>

    <div class="flex h-screen">
        <aside class="hidden w-[280px] flex-col border-r border-slate-200 bg-white md:flex">
            <div class="flex h-20 items-center border-b border-slate-100 px-6">
                <a href="<?= BASE_URL ?>/dashboard" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-lg text-white shadow-lg"><i class="fa-solid fa-cloud"></i></div>
                    <div>
                        <span class="block text-lg font-extrabold leading-none tracking-tight text-slate-900">Eka Cloud</span>
                        <span class="mt-1 block text-[10px] font-bold uppercase tracking-[0.18em] text-blue-600">Developer Platform</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
                <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Platform</p>
                <?php foreach ($menuItems as $item): ?>
                    <?php
                    $isActive = $currentPath === $item['path'] || str_starts_with($currentPath, $item['path'] . '/');
                    $linkClass = $isActive ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950';
                    $iconClass = $isActive ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500';
                    ?>
                    <a href="<?= BASE_URL . $item['path'] ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition <?= $linkClass ?>">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg <?= $iconClass ?>"><i class="fa-solid <?= $item['icon'] ?>"></i></span>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="border-t border-slate-100 p-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-slate-900 font-bold text-white"><?= htmlspecialchars(mb_strtoupper(mb_substr($kullanici['name'] ?? 'E', 0, 1))) ?></div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900"><?= htmlspecialchars($kullanici['name'] ?? 'Kullanıcı') ?></p>
                            <p class="truncate text-xs text-slate-500">Tenant #<?= (int) Core\EkaTenant::id() ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/logout" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600" title="Çıkış"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 flex-shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 md:h-20 md:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-slate-950 text-white md:hidden"><i class="fa-solid fa-cloud"></i></div>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold text-slate-900 sm:text-xl"><?= htmlspecialchars($title ?? 'Genel Bakış') ?></p>
                        <p class="hidden text-xs text-slate-500 sm:block">Eka Developer Cloud yönetim paneli</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?= BASE_URL ?>/notifications" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"><i class="fa-regular fa-bell"></i></a>
                    <a href="<?= BASE_URL ?>/logout" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 md:hidden"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 pb-24 sm:p-6 sm:pb-24 md:p-8 md:pb-8">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 z-40 grid grid-cols-5 border-t border-slate-200 bg-white px-1 py-2 shadow-[0_-8px_30px_rgba(15,23,42,0.08)] md:hidden">
        <?php foreach (array_slice($menuItems, 0, 5) as $item): ?>
            <?php $isActive = $currentPath === $item['path'] || str_starts_with($currentPath, $item['path'] . '/'); ?>
            <a href="<?= BASE_URL . $item['path'] ?>" class="flex min-w-0 flex-col items-center gap-1 rounded-xl px-1 py-1.5 text-[10px] font-semibold <?= $isActive ? 'text-blue-700' : 'text-slate-500' ?>">
                <i class="fa-solid <?= $item['icon'] ?> text-base"></i>
                <span class="max-w-full truncate"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <script>
        window.setTimeout(() => {
            document.querySelectorAll('#toast-container > div').forEach((toast) => {
                toast.classList.add('opacity-0', '-translate-y-2', 'transition', 'duration-300');
                window.setTimeout(() => toast.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>