<?php ob_start(); ?>

<div class="text-center mb-6">
    <p class="text-sm text-gray-600">E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.</p>
</div>

<form action="<?= BASE_URL ?>/forgot-password" method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
        <div class="mt-1">
            <input id="email" name="email" type="email" autocomplete="email" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div>
        <button type="submit" 
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            Bağlantı Gönder
        </button>
    </div>
    
    <div class="mt-4 text-center text-sm text-gray-600">
        <a href="<?= BASE_URL ?>/login" class="font-medium text-blue-600 hover:text-blue-500">Giriş ekranına dön</a>
    </div>
</form>

<?php 
$content = ob_get_clean(); 
$title = 'Şifremi Unuttum';
require __DIR__ . '/../layouts/auth.php'; 
?>
