<?php ob_start(); ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="md:col-span-1">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Geliştirici Araçları</h2>
        <p class="text-sm text-slate-500">Firmanıza atanan özel API anahtarları aracılığıyla platformu kendi yazılımlarınıza entegre edebilirsiniz.</p>
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
            <div class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation"></i> Güvenlik Uyarısı</div>
            Lütfen oluşturulan API anahtarlarını kimseyle paylaşmayın ve front-end projelerde veya GitHub vb. açık kaynak repolarında tutmayın.
        </div>
    </div>

    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="text-base font-bold text-slate-800">API Anahtarları (Keys)</h3>
                
                <form action="<?= BASE_URL ?>/api-keys/generate" method="POST" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 border border-transparent rounded-lg font-medium text-white hover:bg-slate-900 shadow-sm transition-colors text-xs">
                        <i class="fa-solid fa-key mr-2"></i> Yeni Key Oluştur
                    </button>
                </form>
            </div>
            <div class="p-6">
                <?php if(!empty($keys)): ?>
                    <div class="space-y-4">
                        <?php foreach($keys as $key): ?>
                            <div class="border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:border-slate-300 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">REST API</div>
                                        <div class="text-sm font-medium text-slate-500"><i class="fa-regular fa-clock mr-1"></i> <?= date('d M Y', strtotime($key['created_at'])) ?></div>
                                    </div>
                                    <div class="font-mono text-base font-bold text-slate-800 bg-slate-50 px-3 py-2 rounded border border-slate-200 break-all cursor-text select-all">
                                        <?= htmlspecialchars($key['api_key']) ?>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 pt-6 sm:pt-0">
                                    <form action="<?= BASE_URL ?>/api-keys/revoke" method="POST" onsubmit="return confirm('Bu API anahtarını iptal etmek istediğinize emin misiniz? Bu anahtarı kullanan uygulamalarınız erişim hatası alacaktır!');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <input type="hidden" name="id" value="<?= $key['id'] ?>">
                                        <button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm font-medium border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 hover:border-red-300 rounded-lg transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-ban mr-2"></i> Anahtarı İptal Et (Revoke)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4 border border-slate-100">
                            <i class="fa-solid fa-plug-circle-xmark text-2xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-slate-900">Aktif API Anahtarı Yok</h3>
                        <p class="text-sm text-slate-500 mt-1">Erişim sağlamak için yeni bir key oluşturun.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'API Yönetimi';
require __DIR__ . '/../layouts/app.php'; 
?>
