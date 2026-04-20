<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Projeler</h2>
        <p class="text-sm text-slate-500 mt-1">Firmanıza ait projeleri yönetin ve takip edin.</p>
    </div>
    <a href="<?= BASE_URL ?>/projects/create" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 shadow-sm transition-colors text-sm">
        <i class="fa-solid fa-plus mr-2"></i> Yeni Proje Ekle
    </a>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Proje Adı</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Açıklama</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Durum</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Oluşturulma</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach($projects as $project): ?>
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 font-bold border border-indigo-100">
                                    <i class="fa-solid fa-folder"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($project['name']) ?></div>
                                    <div class="text-xs text-slate-500">ID: #<?= $project['id'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">
                            <?= htmlspecialchars($project['description'] ?? 'Açıklama yok') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($project['status'] === 'active'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 border border-green-200 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Devam Ediyor
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 border border-slate-200 text-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span> Arşivlendi
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?= date('d M Y', strtotime($project['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?= BASE_URL ?>/projects/edit?id=<?= $project['id'] ?>" class="w-8 h-8 rounded bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-colors flex items-center justify-center" title="Düzenle">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if(empty($projects)): ?>
        <div class="px-6 py-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4 border border-slate-100">
                <i class="fa-solid fa-folder-open text-2xl"></i>
            </div>
            <h3 class="text-sm font-medium text-slate-900">Proje Bulunamadı</h3>
            <p class="text-sm text-slate-500 mt-1">Sisteminizde kayıtlı hiçbir proje yok.</p>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Projeler';
require __DIR__ . '/../layouts/app.php'; 
?>
