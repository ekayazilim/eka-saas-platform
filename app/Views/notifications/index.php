<?php ob_start(); ?>

<div class="px-6 py-5 border-b border-slate-200 bg-white rounded-t-2xl flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Bildirimler</h2>
        <p class="text-sm text-slate-500 mt-1">Sistemden ve firmanızdan gelen tüm önemli uyarılar.</p>
    </div>
</div>

<div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <div class="divide-y divide-slate-100">
        <?php if(!empty($notifications)): ?>
            <?php foreach($notifications as $notif): ?>
                <div class="px-6 py-5 flex items-start hover:bg-slate-50 transition-colors group <?= !$notif['is_read'] ? 'bg-blue-50/30' : '' ?>">
                    <div class="flex-shrink-0 mt-1 mr-4">
                        <?php if(!$notif['is_read']): ?>
                            <div class="relative w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center border border-blue-200">
                                <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                                <i class="fa-solid fa-bell"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center border border-slate-200">
                                <i class="fa-regular fa-bell"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-4 mb-1">
                            <h4 class="text-sm font-bold <?= !$notif['is_read'] ? 'text-slate-900' : 'text-slate-700' ?>">
                                <?= htmlspecialchars($notif['title']) ?>
                            </h4>
                            <span class="flex-shrink-0 text-xs font-medium text-slate-500 whitespace-nowrap">
                                <i class="fa-regular fa-clock mr-1"></i> <?= date('d M, H:i', strtotime($notif['created_at'])) ?>
                            </span>
                        </div>
                        <p class="text-sm <?= !$notif['is_read'] ? 'text-slate-700 font-medium' : 'text-slate-500' ?> break-words">
                            <?= htmlspecialchars($notif['message']) ?>
                        </p>
                    </div>
                    
                    <?php if(!$notif['is_read']): ?>
                        <div class="flex-shrink-0 ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="<?= BASE_URL ?>/notifications/read" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <input type="hidden" name="id" value="<?= $notif['id'] ?>">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-colors shadow-sm">
                                    <i class="fa-solid fa-check mr-1.5"></i> Okundu İşaretle
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="px-6 py-16 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4 border border-slate-100">
                    <i class="fa-regular fa-bell-slash text-2xl"></i>
                </div>
                <h3 class="text-base font-medium text-slate-900 mb-1">Yeni Bildirim Yok</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto">Şu an için okunmamış veya geçmişe dönük herhangi bir bildiriminiz bulunmuyor.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
$title = 'Bildirimler';
require __DIR__ . '/../layouts/app.php'; 
?>
