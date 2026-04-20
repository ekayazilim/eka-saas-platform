<?php ob_start(); ?>

<form action="<?= BASE_URL ?>/login" method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
        <div class="mt-1">
            <input id="email" name="email" type="email" autocomplete="email" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
        <div class="mt-1">
            <input id="password" name="password" type="password" autocomplete="current-password" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div class="text-sm">
            <a href="<?= BASE_URL ?>/forgot-password" class="font-medium text-blue-600 hover:text-blue-500">Şifremi Unuttum</a>
        </div>
    </div>

    <div>
        <button type="submit" 
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            Giriş Yap
        </button>
    </div>
    
    <div class="mt-4 text-center text-sm text-gray-600">
        Hesabınız yok mu? <a href="<?= BASE_URL ?>/register" class="font-medium text-blue-600 hover:text-blue-500">Yeni hesap oluşturun</a>
    </div>
</form>

<?php 
$content = ob_get_clean(); 
$title = 'Giriş Yap';
require __DIR__ . '/../layouts/auth.php'; 
?>
