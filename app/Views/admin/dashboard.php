<?php ob_start(); ?>

<!-- İstatistik Kartları -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col pt-5 pb-6 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-blue-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <h3 class="text-sm font-semibold text-slate-500 tracking-wide uppercase">Kayıtlı Firmalar</h3>
            <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-inner shadow-blue-400">
                <i class="fa-solid fa-building text-lg"></i>
            </div>
        </div>
        <div class="mt-4 z-10 relative">
            <p class="text-3xl font-extrabold text-slate-800"><?= $totalTenants ?></p>
            <p class="text-sm text-green-500 font-medium mt-1"><i class="fa-solid fa-arrow-trend-up"></i> Aktif firmalar</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col pt-5 pb-6 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-indigo-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <h3 class="text-sm font-semibold text-slate-500 tracking-wide uppercase">Toplam Kullanıcı</h3>
            <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center shadow-inner shadow-indigo-400">
                <i class="fa-solid fa-users text-lg"></i>
            </div>
        </div>
        <div class="mt-4 z-10 relative">
            <p class="text-3xl font-extrabold text-slate-800"><?= $totalUsers ?></p>
            <p class="text-sm text-indigo-500 font-medium mt-1">Platform geneli</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col pt-5 pb-6 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-emerald-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <h3 class="text-sm font-semibold text-slate-500 tracking-wide uppercase">Aylık Gelir</h3>
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-inner shadow-emerald-400">
                <i class="fa-solid fa-wallet text-lg"></i>
            </div>
        </div>
        <div class="mt-4 z-10 relative">
            <p class="text-3xl font-extrabold text-slate-800">0,00 ₺</p>
            <p class="text-sm text-emerald-500 font-medium mt-1"><i class="fa-solid fa-chart-line"></i> Tahmini ciro</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col pt-5 pb-6 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-amber-50 w-24 h-24 rounded-full transition-transform group-hover:scale-110 duration-500"></div>
        <div class="flex items-center justify-between z-10 relative">
            <h3 class="text-sm font-semibold text-slate-500 tracking-wide uppercase">Durum</h3>
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-inner shadow-amber-400">
                <i class="fa-solid fa-server text-lg"></i>
            </div>
        </div>
        <div class="mt-4 z-10 relative">
            <p class="text-3xl font-extrabold text-slate-800">Sistem Açık</p>
            <p class="text-sm text-amber-500 font-medium mt-1">V1.0.0 Yayında</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Platform Geneli Son Aktiviteler</h3>
            <p class="text-sm text-slate-500 mt-1">Sistemdeki tüm firmaların ortak işlem geçmişi.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/logs" class="text-sm font-medium text-blue-600 hover:text-blue-800 bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">Tümünü Gör</a>
    </div>
    <div class="divide-y divide-slate-100">
        <?php if(!empty($recentLogs)): ?>
            <?php foreach($recentLogs as $log): ?>
                <div class="px-6 py-4 flex items-start hover:bg-slate-50 transition-colors">
                    <div class="mt-1">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-code-commit"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-xs font-bold leading-none bg-slate-100 border border-slate-200 text-slate-600 px-2.5 py-1 rounded-md">
                                <i class="fa-regular fa-building text-slate-400 mr-1"></i> Firma ID: <?= $log['tenant_id'] ?? 'Sistem' ?>
                            </span>
                            <span class="text-xs font-bold leading-none bg-blue-50 border border-blue-100 text-blue-600 px-2.5 py-1 rounded-md">
                                <?= $log['action'] ?>
                            </span>
                        </div>
                        <p class="text-sm text-slate-800 font-medium"><?= htmlspecialchars($log['details']) ?></p>
                        <p class="text-xs text-slate-500 mt-1.5"><i class="fa-regular fa-clock text-slate-400 mr-1"></i> <?= date('d.m.Y H:i:s', strtotime($log['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="px-6 py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-slate-900">Henüz aktivite yok</h3>
                <p class="text-sm text-slate-500 mt-1">Sisteminizde hiç hareket olmadı.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Sistem İstatistikleri';
require __DIR__ . '/../layouts/admin.php'; 
?>
