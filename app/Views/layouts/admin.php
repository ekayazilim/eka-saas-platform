<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Süper Admin | EkaYazılım</title>
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
    <!-- Fancybox & Flatpickr (Global Rules) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .glass-sidebar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
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
        <aside class="w-[280px] glass-sidebar text-white hidden md:flex flex-col relative z-20">
            <!-- Brand -->
            <div class="h-20 flex items-center px-8 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-shield-halved text-white text-lg"></i>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold tracking-tight text-white block leading-none pt-1">EkaYazılım</span>
                        <span class="text-[10px] uppercase font-bold text-blue-400 tracking-widest block mt-0.5">Süper Admin</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto">
                <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Ana Menü</p>
                <?php
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $menuItems = [
                    ['path' => '/admin/dashboard', 'icon' => 'fa-chart-pie', 'label' => 'İstatistikler'],
                    ['path' => '/admin/tenants', 'icon' => 'fa-building', 'label' => 'Müşteri Firmalar'],
                    ['path' => '/admin/users', 'icon' => 'fa-users-gear', 'label' => 'Kullanıcılar'],
                    ['path' => '/admin/plans', 'icon' => 'fa-tags', 'label' => 'Abonelik Planları'],
                    ['path' => '/admin/logs', 'icon' => 'fa-bars-staggered', 'label' => 'Aktivite Logları'],
                ];
                
                foreach ($menuItems as $item):
                    $isActive = strpos($currentPath, $item['path']) === 0;
                    $bgClass = $isActive ? 'bg-blue-600/10 text-blue-400 font-semibold' : 'text-slate-400 hover:bg-white/5 hover:text-white';
                    $iconColor = $isActive ? 'text-blue-500' : 'text-slate-500 group-hover:text-slate-300';
                ?>
                    <a href="<?= BASE_URL . $item['path'] ?>" border-transparent
                        class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $bgClass ?>">
                        <i class="fa-solid <?= $item['icon'] ?> w-6 <?= $iconColor ?> text-lg transition-colors"></i> 
                        <span class="ml-1 text-sm"><?= $item['label'] ?></span>
                        <?php if($isActive): ?>
                            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- User Widget -->
            <div class="p-4 mt-auto border-t border-white/5">
                <div class="bg-white/5 rounded-2xl p-4 flex items-center justify-between border border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700 text-blue-400 font-bold">
                            SU
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold text-white truncate">Süper Yetkili</p>
                            <p class="text-xs text-slate-400 truncate">Sistem Yöneticisi</p>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/logout" class="w-8 h-8 rounded-full bg-red-500/10 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Çıkış Yap">
                        <i class="fa-solid fa-power-off text-sm"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-64 bg-blue-600/5 z-0 pointer-events-none"></div>
            
            <header class="h-20 flex items-center justify-between px-8 z-10 relative">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight"><?= $title ?? 'Yönetim Paneli' ?></h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-white rounded-full px-4 py-2 text-xs font-semibold text-slate-600 border border-slate-200 shadow-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Sistem Aktif
                    </div>
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