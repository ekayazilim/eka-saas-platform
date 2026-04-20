<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Abonelik Planları</h2>
        <p class="text-sm text-slate-500 mt-1">Sistemdeki üyelik paketlerini yönetin.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/plans/create" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 shadow-sm transition-colors text-sm">
        <i class="fa-solid fa-plus mr-2"></i> Yeni Plan Ekle
    </a>
</div>

<div class="p-6 bg-slate-50 rounded-b-2xl border border-t-0 border-slate-200">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($plans as $plan): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative hover:shadow-md transition-shadow group flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($plan['name']) ?></h3>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="<?= BASE_URL ?>/admin/plans/edit?id=<?= $plan['id'] ?>" class="text-slate-400 hover:text-blue-600 transition-colors" title="Düzenle">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="<?= BASE_URL ?>/admin/plans/delete" method="POST" class="inline" onsubmit="return confirm('Bu planı silmek istediğinize emin misiniz?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <input type="hidden" name="id" value="<?= $plan['id'] ?>">
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="Sil">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-3xl font-extrabold text-blue-600 mb-6 tracking-tight">
                    <?= number_format($plan['price'], 2) ?> ₺<span class="text-sm text-slate-400 font-normal ml-1">/ay</span>
                </div>
                
                <ul class="space-y-3 flex-1 mb-6">
                    <li class="flex items-center text-sm text-slate-700">
                        <div class="w-6 h-6 rounded-full bg-green-50 flex items-center justify-center mr-3 text-green-500">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="font-medium mr-1"><?= $plan['user_limit'] ?></span> Kullanıcı Limiti
                    </li>
                    <li class="flex items-center text-sm text-slate-700">
                        <div class="w-6 h-6 rounded-full bg-green-50 flex items-center justify-center mr-3 text-green-500">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="font-medium mr-1"><?= $plan['project_limit'] ?></span> Proje Limiti
                    </li>
                    <li class="flex items-center text-sm text-slate-700">
                        <div class="w-6 h-6 rounded-full bg-green-50 flex items-center justify-center mr-3 text-green-500">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="font-medium mr-1"><?= number_format($plan['api_limit']) ?></span> API İstek / Ay
                    </li>
                </ul>
                
                <div class="pt-4 border-t border-slate-100 mt-auto">
                    <span class="text-xs text-slate-400">Plan ID: #<?= $plan['id'] ?> &bull; Slug: <?= htmlspecialchars($plan['slug']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Abonelik Planları';
require __DIR__ . '/../../layouts/admin.php'; 
?>
