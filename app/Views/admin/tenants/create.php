<?php ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="<?= BASE_URL ?>/admin/tenants" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors mr-4">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Yeni Firma Ekle</h2>
            <p class="text-sm text-slate-500">Platforma sistem üzerinden manuel bir firma (tenant) ekleyin.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="<?= BASE_URL ?>/admin/tenants/store" method="POST" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Firma Adı (Şirket İsmi)</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Örn: Eka Yazılım A.Ş.">
                    <p class="mt-1 text-xs text-slate-500">Sistem Slug değerini isimden otomatik üretecektir.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Abonelik Planı</label>
                    <select name="plan_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                        <?php foreach($plans as $plan): ?>
                            <option value="<?= $plan['id'] ?>"><?= htmlspecialchars($plan['name']) ?> (<?= number_format($plan['price'], 2) ?> ₺)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hesap Durumu</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                        <option value="active">Aktif (Kullanabilir)</option>
                        <option value="suspended">Askıya Alınmış / Pasif</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/tenants" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">İptal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    Firmayı Oluştur
                </button>
            </div>
            
            <div class="mt-6 rounded-lg bg-blue-50 border border-blue-100 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-blue-400 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Bilgi</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            Buradan sadece kurum profilini oluşturursunuz. Oluşturduktan sonra Kullanıcılar menüsünden bu firmaya atanmış bir <b>Owner</b> hesabı yaratmanız gerekir ki yetkililer giriş yapabilsin.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Yeni Firma Ekle';
require __DIR__ . '/../../layouts/admin.php'; 
?>
