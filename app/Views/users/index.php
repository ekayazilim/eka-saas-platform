<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Personel / Kadro</h2>
        <p class="text-sm text-slate-500 mt-1">Firmanız içindeki sisteme erişebilen üyeleri yönetin.</p>
    </div>
    <a href="<?= BASE_URL ?>/users/create" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 shadow-sm transition-colors text-sm">
        <i class="fa-solid fa-plus mr-2"></i> Ekip Üyesi Ekle
    </a>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kullanıcı Profil</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">İzin / Rol</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Platform Katılımı</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach($users as $user): ?>
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 font-bold border border-indigo-100">
                                    <?= substr(htmlspecialchars($user['name']), 0, 1) ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-slate-900">
                                        <?= htmlspecialchars($user['name']) ?>
                                        <?php if($user['id'] == Core\EkaAuth::id()): ?>
                                            <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Siz</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($user['role'] === 'owner'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-purple-50 border border-purple-200 text-purple-700">
                                    Firma Sahibi (Kurucu)
                                </span>
                            <?php elseif($user['role'] === 'admin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 border border-blue-200 text-blue-700">
                                    Yönetici (Admin)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 border border-slate-200 text-slate-700">
                                    Standart Personel
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if($user['id'] != Core\EkaAuth::id() && $user['role'] !== 'owner'): ?>
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="<?= BASE_URL ?>/users/delete" method="POST" class="inline" onsubmit="return confirm('Bu kullanıcıyı hesaptan silmek istediğinize emin misiniz? Sistem erişimini tamamen kaybedecektir.');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 transition-colors flex items-center justify-center" title="Kadrodan Çıkar (Sil)">
                                            <i class="fa-solid fa-user-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if(empty($users)): ?>
        <div class="px-6 py-12 text-center">
            <h3 class="text-sm font-medium text-slate-900">Kullanıcı Bulunamadı</h3>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Ekip Kullanıcıları';
require __DIR__ . '/../layouts/app.php'; 
?>
