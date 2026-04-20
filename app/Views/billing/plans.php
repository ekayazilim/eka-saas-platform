<?php ob_start(); ?>

<div class="text-center mb-10">
    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Platform Abonelik Planları</h2>
    <p class="mt-4 text-lg text-gray-500">İhtiyacınıza uygun olan paketi seçerek firmanızı büyütün.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
    <?php foreach($plans as $plan): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 flex flex-col relative <?= $plan['slug'] === 'pro' ? 'ring-2 ring-blue-500' : '' ?>">
            <?php if($plan['slug'] === 'pro'): ?>
                <div class="absolute top-0 right-6 transform -translate-y-1/2">
                    <span class="bg-blue-500 text-white text-xs font-bold uppercase tracking-wider py-1 px-3 rounded-full">En Popüler</span>
                </div>
            <?php endif; ?>
            
            <h3 class="text-2xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($plan['name']) ?></h3>
            <div class="mb-6">
                <?php if($plan['price'] > 0): ?>
                    <span class="text-4xl font-extrabold text-blue-600"><?= number_format($plan['price'], 2) ?> ₺</span>
                    <span class="text-base font-medium text-gray-500">/ay</span>
                <?php else: ?>
                    <span class="text-4xl font-extrabold text-blue-600">Ücretsiz</span>
                <?php endif; ?>
            </div>
            
            <ul class="space-y-4 flex-1 mb-8">
                <li class="flex items-start">
                    <div class="flex-shrink-0"><i class="fa-solid fa-check text-green-500 mt-1"></i></div>
                    <p class="ml-3 text-base text-gray-700">Maksimum <b><?= $plan['user_limit'] ?></b> ekip üyesi</p>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0"><i class="fa-solid fa-check text-green-500 mt-1"></i></div>
                    <p class="ml-3 text-base text-gray-700">Maksimum <b><?= $plan['project_limit'] ?></b> proje oluşturma</p>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0"><i class="fa-solid fa-check text-green-500 mt-1"></i></div>
                    <p class="ml-3 text-base text-gray-700">Aylık <b><?= number_format($plan['api_limit']) ?></b> API isteği</p>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0"><i class="fa-solid fa-check text-green-500 mt-1"></i></div>
                    <p class="ml-3 text-base text-gray-700">7/24 E-posta desteği</p>
                </li>
            </ul>
            
            <button type="button" class="mt-auto block w-full bg-blue-600 border border-transparent rounded-lg py-3 px-4 text-center text-sm font-medium text-white hover:bg-blue-700 transition">
                Planı Seç
            </button>
        </div>
    <?php endforeach; ?>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Abonelik Planları';
require __DIR__ . '/../layouts/app.php'; 
?>
