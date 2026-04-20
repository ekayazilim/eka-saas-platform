<?php ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="<?= BASE_URL ?>/admin/plans" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors mr-4">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Planı Düzenle</h2>
            <p class="text-sm text-slate-500"><?= htmlspecialchars($plan['name']) ?> isimli paketi güncelliyorsunuz.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="<?= BASE_URL ?>/admin/plans/update" method="POST" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="id" value="<?= $plan['id'] ?>">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Plan Adı</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($plan['name']) ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Aylık Ücret (₺)</label>
                    <input type="number" step="0.01" name="price" value="<?= $plan['price'] ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kullanıcı Limiti</label>
                        <input type="number" name="user_limit" value="<?= $plan['user_limit'] ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Proje Limiti</label>
                        <input type="number" name="project_limit" value="<?= $plan['project_limit'] ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Aylık API İstek Limiti</label>
                    <input type="number" name="api_limit" value="<?= $plan['api_limit'] ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/plans" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">İptal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    Planı Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Planı Düzenle';
require __DIR__ . '/../../layouts/admin.php'; 
?>
