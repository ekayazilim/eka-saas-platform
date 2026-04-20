<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Firmalar (Tenants)</h2>
        <p class="text-sm text-slate-500 mt-1">Platformdaki üye firmaları yönetin.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/tenants/create" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 shadow-sm transition-colors text-sm">
        <i class="fa-solid fa-plus mr-2"></i> Yeni Firma Ekle
    </a>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Firma Bilgisi</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Abonelik Planı</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Durum</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kayıt Tarihi</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach($tenants as $tenant): ?>
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 font-bold border border-blue-100">
                                    <?= substr(htmlspecialchars($tenant['name']), 0, 1) ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($tenant['name']) ?></div>
                                    <div class="text-xs text-slate-500">ID: #<?= $tenant['id'] ?> | Slug: <?= htmlspecialchars($tenant['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 border border-slate-200 text-slate-700">
                                <i class="fa-solid fa-box mr-1.5 opacity-50"></i> <?= htmlspecialchars($tenant['plan_name'] ?? 'Plan Yok') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($tenant['status'] === 'active'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 border border-green-200 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 border border-red-200 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Pasif / Askıda
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?= date('d M Y', strtotime($tenant['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="<?= BASE_URL ?>/admin/tenants/toggle" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="id" value="<?= $tenant['id'] ?>">
                                    <button type="submit" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-amber-600 hover:border-amber-200 transition-colors flex items-center justify-center" title="<?= $tenant['status'] === 'active' ? 'Askıya Al' : 'Aktifleştir' ?>">
                                        <i class="fa-solid <?= $tenant['status'] === 'active' ? 'fa-pause' : 'fa-play' ?>"></i>
                                    </button>
                                </form>
                                <a href="<?= BASE_URL ?>/admin/tenants/edit?id=<?= $tenant['id'] ?>" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-colors flex items-center justify-center" title="Düzenle">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="<?= BASE_URL ?>/admin/tenants/delete" method="POST" class="inline" onsubmit="return confirm('DİKKAT: Bu firmayı sildiğinizde, ona ait tüm projeler, kullanıcılar ve api anahtarları da KALICI olarak SİLİNECEKTİR. Emin misiniz?');">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="id" value="<?= $tenant['id'] ?>">
                                    <button type="submit" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 transition-colors flex items-center justify-center" title="Kalıcı Olarak Sil">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if(empty($tenants)): ?>
        <div class="px-6 py-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4 border border-slate-100">
                <i class="fa-solid fa-building text-2xl"></i>
            </div>
            <h3 class="text-sm font-medium text-slate-900">Firma Bulunamadı</h3>
            <p class="text-sm text-slate-500 mt-1">Sisteminizde kayıtlı hiçbir firma yok.</p>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Firmalar (Tenants)';
require __DIR__ . '/../../layouts/admin.php'; 
?>
