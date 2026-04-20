<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Platform Kullanıcıları</h2>
        <p class="text-sm text-slate-500 mt-1">Platforma kayıtlı tüm kullanıcıları ve yetkilerini yönetin.</p>
    </div>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kullanıcı Profil</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanımlı Firma</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Sistem Rolü</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kayıt Tarihi</th>
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
                                    <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($user['name']) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($user['tenant_id']): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 border border-blue-100 text-blue-700">
                                    <i class="fa-solid fa-building mr-1.5 opacity-50"></i> Firma ID: #<?= $user['tenant_id'] ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 border border-slate-200 text-slate-600">
                                    <i class="fa-solid fa-server mr-1.5 opacity-50"></i> Sistem Otoritesi
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($user['role'] === 'super_admin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 border border-red-200 text-red-700">
                                    Süper Admin
                                </span>
                            <?php elseif($user['role'] === 'owner'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-purple-50 border border-purple-200 text-purple-700">
                                    Firma Sahibi (Kurucu)
                                </span>
                            <?php elseif($user['role'] === 'admin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 border border-amber-200 text-amber-700">
                                    Müşteri - Yöneticisi
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 border border-gray-200 text-gray-700">
                                    Personel (Member)
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?= date('d M Y, H:i', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?= BASE_URL ?>/admin/users/edit?id=<?= $user['id'] ?>" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-colors flex items-center justify-center" title="Düzenle">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <?php if($user['id'] != \Core\EkaAuth::id()): ?>
                                    <form action="<?= BASE_URL ?>/admin/users/delete" method="POST" class="inline" onsubmit="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz?');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 transition-colors flex items-center justify-center" title="Kalıcı Olarak Sil">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Platform Kullanıcıları';
require __DIR__ . '/../../layouts/admin.php'; 
?>
