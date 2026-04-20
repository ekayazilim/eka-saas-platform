<?php ob_start(); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1 border border-gray-100 bg-white rounded-xl shadow-sm overflow-hidden text-center">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white relative">
            <h3 class="text-xs uppercase font-bold tracking-wider opacity-80 mb-1">Mevcut Planınız</h3>
            <div class="text-4xl font-extrabold"><?= htmlspecialchars($currentPlan['name']) ?></div>
            <?php if($currentPlan['price'] > 0): ?>
                <div class="mt-4 text-3xl font-bold"><?= number_format($currentPlan['price'], 2) ?> ₺<span class="text-xl font-normal opacity-80">/ay</span></div>
            <?php else: ?>
                <div class="mt-4 text-3xl font-bold">Ücretsiz</div>
            <?php endif; ?>
        </div>
        
        <div class="p-6 bg-white space-y-4 text-left">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fa-solid fa-check text-green-500 w-5"></i> <span>Maksimum <b><?= $currentPlan['user_limit'] ?></b> kullanıcı</span>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fa-solid fa-check text-green-500 w-5"></i> <span>Maksimum <b><?= $currentPlan['project_limit'] ?></b> proje</span>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fa-solid fa-check text-green-500 w-5"></i> <span><b><?= number_format($currentPlan['api_limit']) ?></b> API isteği (aylık)</span>
            </div>
            
            <div class="pt-6">
                <a href="<?= BASE_URL ?>/billing/plans" class="block w-full text-center bg-blue-50 text-blue-700 font-medium py-3 rounded-lg hover:bg-blue-100 transition">
                    Planı Yükselt
                </a>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Fatura Geçmişi</h3>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(!empty($invoices)): ?>
                        <?php foreach($invoices as $inv): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= date('d.m.Y', strtotime($inv['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= number_format($inv['amount'], 2) ?> ₺
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($inv['status'] === 'paid'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ödendi</span>
                                    <?php elseif($inv['status'] === 'unpaid'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Ödenmedi</span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">İptal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">Fatura geçmişiniz bulunmuyor.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Abonelik & Faturalar';
require __DIR__ . '/../layouts/app.php'; 
?>
