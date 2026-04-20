<?php ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="<?= BASE_URL ?>/admin/users" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors mr-4">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kullanıcıyı Düzenle</h2>
            <p class="text-sm text-slate-500"><?= htmlspecialchars($user['name']) ?> profil bilgilerini ve yetkilerini güncelleyin.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="<?= BASE_URL ?>/admin/users/update" method="POST" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kullanıcı Adı Soyadı</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Yeni Parola (İsteğe Bağlı)</label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                    <p class="mt-1 text-xs text-slate-500">Parolayı değiştirdiğinizde bcrypt ile şifrelenerek kaydeditlir.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Bağlı Olduğu Firma</label>
                        <select name="tenant_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                            <option value="">-- Kök Sistem Yetkilisi (Firma Yok) --</option>
                            <?php foreach($tenants as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= $t['id'] == $user['tenant_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['name']) ?> (#<?= $t['id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Sistem İçi Rol (Yetki)</label>
                        <select name="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                            <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Süper Admin (Tam Erişim)</option>
                            <option value="owner" <?= $user['role'] === 'owner' ? 'selected' : '' ?>>Firma Sahibi (Owner)</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Firma Yöneticisi (Admin)</option>
                            <option value="member" <?= $user['role'] === 'member' ? 'selected' : '' ?>>Standart Personel (Member)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/admin/users" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">İptal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    Kullanıcıyı Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Kullanıcı Düzenle';
require __DIR__ . '/../../layouts/admin.php'; 
?>
