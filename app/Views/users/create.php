<?php ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="<?= BASE_URL ?>/users" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors mr-4">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Personel Ekle</h2>
            <p class="text-sm text-slate-500">Firmanızı yönetmek ve kullanmak için yeni bir hesap ekleyin.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="<?= BASE_URL ?>/users/store" method="POST" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kullanıcı Adı Soyadı</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Örn: Ahmet Yılmaz">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="E-posta adresi giriş yaparken kullanılacak">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Şifre Ataması</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Güçlü bir parola belirleyin">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Yetki (Rol)</label>
                    <select name="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                        <option value="member">Standart Personel (Sınırlı Erişim)</option>
                        <option value="admin">Yönetici (Tam Yetkili Kadro Erişimi)</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/users" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">İptal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    Personeli Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Yeni Kullanıcı (Personel)';
require __DIR__ . '/../layouts/app.php'; 
?>
