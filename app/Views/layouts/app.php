<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Paneli | EkaYazılım</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .glass-sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden">
    
    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-5 right-5 space-y-3 z-50 pointer-events-none">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="pointer-events-auto bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-300 animate-slide-in">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold">Başarılı</h4>
                    <p class="text-xs"><?= htmlspecialchars($_SESSION['success']) ?></p>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="pointer-events-auto bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-300 animate-slide-in">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold">Hata</h4>
                    <p class="text-xs"><?= htmlspecialchars($_SESSION['error']) ?></p>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-[280px] glass-sidebar hidden md:flex flex-col relative z-20 shadow-[0_0_15px_rgba(0,0,0,0.02)]">
            <!-- Brand -->
            <div class="h-20 flex items-center px-8 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-cloud text-white text-lg"></i>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold tracking-tight text-slate-800 block leading-none pt-1">Workspace</span>
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-widest block mt-0.5">EkaYazılım Platf.</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto">
                <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Modüller</p>
                <?php
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $menuItems = [
                    ['path' => '/dashboard', 'icon' => 'fa-house', 'label' => 'Arayüz (Dashboard)'],
                    ['path' => '/projects', 'icon' => 'fa-folder-open', 'label' => 'Projeler'],
                    ['path' => '/users', 'icon' => 'fa-users', 'label' => 'Personel Kadrosu'],
                    ['path' => '/api-keys', 'icon' => 'fa-key', 'label' => 'API Ayarları'],
                    ['path' => '/notifications', 'icon' => 'fa-bell', 'label' => 'Bildirim Merkezi'],
                    ['path' => '/billing', 'icon' => 'fa-credit-card', 'label' => 'Abonelik & Fatura'],
                ];
                
                foreach ($menuItems as $item):
                    $isActive = strpos($currentPath, $item['path']) === 0 && ($item['path'] !== '/' || $currentPath === '/');
                    $bgClass = $isActive ? 'bg-blue-50 text-blue-700 font-semibold border-blue-500' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-transparent';
                    $iconColor = $isActive ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600';
                ?>
                    <a href="<?= BASE_URL . $item['path'] ?>" 
                        class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group border-l-4 <?= $bgClass ?>">
                        <i class="fa-solid <?= $item['icon'] ?> w-6 <?= $iconColor ?> text-lg transition-colors"></i> 
                        <span class="ml-1 text-sm"><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- User Widget -->
            <div class="p-4 mt-auto border-t border-slate-100 bg-slate-50/50">
                <div class="bg-white rounded-2xl p-4 flex items-center justify-between border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-600 font-bold flex-shrink-0">
                            <?= substr(Core\EkaAuth::user()['name'], 0, 1) ?>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars(Core\EkaAuth::user()['name']) ?></p>
                            <p class="text-[10px] uppercase font-bold text-slate-500 truncate mt-0.5">Firması ID: #<?= Core\EkaTenant::id() ?></p>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/logout" class="w-8 h-8 flex-shrink-0 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" title="Çıkış Yap">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50 z-0 pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
            
            <header class="h-20 flex items-center justify-between px-8 z-10 relative bg-white/50 backdrop-blur-sm border-b border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)]">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight"><?= $title ?? 'Dashboard' ?></h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>/notifications" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition-colors relative">
                        <i class="fa-regular fa-bell"></i>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8 z-10 relative">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    
    <script>
        setTimeout(() => {
            const toasts = document.querySelectorAll('#toast-container > div');
            toasts.forEach(toast => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>