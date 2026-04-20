<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Sistem Aktivite Logları</h2>
        <p class="text-sm text-slate-500 mt-1">Platform üzerinde gerçekleşen tüm hareketleri, kayıtları ve oturumları izleyin.</p>
    </div>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tarih / Saat</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Firma Bilgisi</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksiyon Türü</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">İşlem Detayları</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                <?php foreach($logs as $log): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                            <i class="fa-regular fa-clock text-slate-400 mr-1.5 w-4 text-center"></i>
                            <?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($log['tenant_id']): ?>
                                <span class="inline-flex items-center text-sm font-medium text-slate-900">
                                    <i class="fa-regular fa-building text-blue-500 mr-2 w-4 text-center"></i> 
                                    Firma ID: #<?= $log['tenant_id'] ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center text-sm font-medium text-slate-500">
                                    <i class="fa-solid fa-server mr-2 w-4 text-center"></i> 
                                    Sistem Geneli
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $actionColorMap = [
                                    'user_login' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                    'user_logout' => 'bg-slate-100 border-slate-200 text-slate-700',
                                    'tenant_registered' => 'bg-purple-50 border-purple-200 text-purple-700',
                                    'project_created' => 'bg-blue-50 border-blue-200 text-blue-700',
                                    'api_key_generated' => 'bg-amber-50 border-amber-200 text-amber-700',
                                ];
                                $colorClass = $actionColorMap[$log['action']] ?? 'bg-blue-50 border-blue-200 text-blue-700';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold font-mono tracking-tight border <?= $colorClass ?>">
                                <?= htmlspecialchars($log['action']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            <?= htmlspecialchars($log['details']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if(empty($logs)): ?>
        <div class="px-6 py-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4 border border-slate-100">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
            <h3 class="text-sm font-medium text-slate-900">Log Kaydı Yok</h3>
            <p class="text-sm text-slate-500 mt-1">Sisteminizde hiç hareket olmadı.</p>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Tüm Sistem Logları';
require __DIR__ . '/../../layouts/admin.php'; 
?>
