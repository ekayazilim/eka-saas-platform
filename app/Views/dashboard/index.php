<?php ob_start(); ?>

<!-- Karşılama Alanı -->
<div class="mb-8">
    <h2 class="text-3xl font-extrabold text-slate-800">Hoş Geldiniz, <?= htmlspecialchars(Core\EkaAuth::user()['name']) ?> 👋</h2>
    <p class="text-slate-500 mt-2">Firmanızın özet verilerine ve kullanım istatistiklerine göz atın.</p>
</div>

<!-- İstatistik Kartları -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute -right-6 -top-6 bg-blue-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-inner shadow-blue-400">
                <i class="fa-solid fa-folder-open text-xl"></i>
            </div>
            <div class="text-right">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Aktif Projeler</h3>
                <p class="text-3xl font-extrabold text-slate-800 mt-1"><?= $totalProjects ?> <span class="text-lg text-slate-400 font-normal">/ <?= $plan['project_limit'] ?></span></p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 z-10 relative">
            <p class="text-sm text-slate-500">Kalan hak: <?= $plan['project_limit'] - $totalProjects ?> proje</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute -right-6 -top-6 bg-indigo-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <div class="w-12 h-12 rounded-xl bg-indigo-500 text-white flex items-center justify-center shadow-inner shadow-indigo-400">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div class="text-right">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Ekip Üyeleri</h3>
                <p class="text-3xl font-extrabold text-slate-800 mt-1"><?= $totalUsers ?> <span class="text-lg text-slate-400 font-normal">/ <?= $plan['user_limit'] ?></span></p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 z-10 relative">
            <p class="text-sm text-slate-500">Kalan hak: <?= $plan['user_limit'] - $totalUsers ?> kullanıcı</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute -right-6 -top-6 bg-emerald-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-inner shadow-emerald-400">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <div class="text-right">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">API Kullanımı</h3>
                <p class="text-3xl font-extrabold text-slate-800 mt-1"><?= number_format($totalKeys) ?></p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 z-10 relative">
            <p class="text-sm text-slate-500">Aylık Limit: <?= number_format($plan['api_limit']) ?> istek</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Son Eklenen Projeler -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Üzerinde Çalışılan Projeler</h3>
                <a href="<?= BASE_URL ?>/projects" class="text-sm font-medium text-blue-600 hover:text-blue-800">Tümünü Yönet &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if(!empty($recentProjects)): ?>
                    <?php foreach($recentProjects as $project): ?>
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-4 border border-blue-100">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900"><?= htmlspecialchars($project['name']) ?></h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Durum: <span class="<?= $project['status'] === 'active' ? 'text-green-600' : 'text-slate-500' ?>"><?= ucfirst($project['status']) ?></span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="<?= BASE_URL ?>/projects/edit?id=<?= $project['id'] ?>" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-blue-600 transition-opacity p-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-10 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-3 border border-slate-100">
                            <i class="fa-solid fa-folder-open text-xl"></i>
                        </div>
                        <p class="text-sm text-slate-500">Henüz hiçbir proje oluşturmadınız.</p>
                        <a href="<?= BASE_URL ?>/projects/create" class="mt-3 inline-block text-sm font-medium text-blue-600 hover:underline">Proje Oluştur</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div>
        <!-- Hesap Bilgileri / Mini Fatura -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-md border border-slate-700 p-6 text-white relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10">
                <i class="fa-solid fa-chart-pie text-9xl transform translate-x-4 -translate-y-4"></i>
            </div>
            
            <h3 class="text-lg font-bold mb-1 relative z-10">Abonelik Durumu</h3>
            <p class="text-sm text-slate-400 mb-6 relative z-10">Mevcut planınız ve kullanımınız.</p>
            
            <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700 mb-6 relative z-10 backdrop-blur-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-slate-300">Geçerli Plan</span>
                    <span class="text-sm font-bold text-blue-400"><?= htmlspecialchars($plan['name']) ?></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-slate-300">Yenileme Tarihi</span>
                    <span class="text-sm font-bold">Yakında</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-700">
                    <span class="text-sm font-medium text-slate-300">Aylık Tutar</span>
                    <span class="text-lg font-bold"><?= number_format($plan['price'], 2) ?> ₺</span>
                </div>
            </div>
            
            <div class="relative z-10">
                <a href="<?= BASE_URL ?>/billing" class="block w-full py-2.5 px-4 bg-white/10 hover:bg-white/20 border border-white/10 rounded-lg text-center text-sm font-medium transition-colors">
                    Fatura & Abonelik Yönetimi
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Dashboard';
require __DIR__ . '/../layouts/app.php'; 
?>
