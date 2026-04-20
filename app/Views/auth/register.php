<?php ob_start(); ?>

<form action="<?= BASE_URL ?>/register" method="POST" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <div>
        <label for="company_name" class="block text-sm font-medium text-gray-700">Firma Adı</label>
        <div class="mt-1">
            <input id="company_name" name="company_name" type="text" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad</label>
        <div class="mt-1">
            <input id="name" name="name" type="text" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
        <div class="mt-1">
            <input id="email" name="email" type="email" autocomplete="email" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Şifre (En az 6 karakter)</label>
        <div class="mt-1">
            <input id="password" name="password" type="password" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div>
        <button type="submit" 
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            Hesap Oluştur
        </button>
    </div>
    
    <div class="mt-4 text-center text-sm text-gray-600">
        Zaten hesabınız var mı? <a href="<?= BASE_URL ?>/login" class="font-medium text-blue-600 hover:text-blue-500">Giriş yapın</a>
    </div>
</form>

<?php 
$content = ob_get_clean(); 
$title = 'Yeni Hesap Oluştur';
require __DIR__ . '/../layouts/auth.php'; 
?>
