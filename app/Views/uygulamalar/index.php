<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900">Uygulamalar</h2>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600"><?= count($uygulamalar) ?> / <?= (int) $limitler['application_limit'] ?></span>
            </div>
            <p class="mt-1 text-sm text-slate-500">React, Next.js, Node.js, Python ve Docker uygulamalarınızı tek panelden yönetin.</p>
        </div>
        <a href="<?= BASE_URL ?>/uygulamalar/olustur" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"><i class="fa-solid fa-plus mr-2"></i>Yeni Uygulama</a>
    </div>

    <?php if (!$dokployHazir): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
            <div class="flex items-start gap-3"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i><div><p class="font-semibold">Deployment altyapısı yapılandırılmamış</p><p class="mt-1 text-sm text-amber-800">DOKPLOY_URL ve DOKPLOY_API_KEY tanımlanmadan deployment işlemleri çalışmaz.</p></div></div>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Paket</p><p class="mt-2 text-lg font-bold text-slate-900"><?= htmlspecialchars($paket['name'] ?? '-') ?></p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">RAM Limiti</p><p class="mt-2 text-lg font-bold text-slate-900"><?= number_format((int) $limitler['memory_limit_mb'] / 1024, 1) ?> GB</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">CPU Limiti</p><p class="mt-2 text-lg font-bold text-slate-900"><?= number_format((int) $limitler['cpu_limit_millicores'] / 1000, 2) ?> vCPU</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Disk Limiti</p><p class="mt-2 text-lg font-bold text-slate-900"><?= number_format((int) $limitler['storage_limit_mb'] / 1024, 0) ?> GB</p></div>
    </div>

    <?php if (empty($uygulamalar)): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-600"><i class="fa-solid fa-cubes"></i></div>
            <h3 class="mt-5 text-lg font-bold text-slate-900">Henüz uygulamanız yok</h3>
            <p class="mx-auto mt-2 max-w-xl text-sm text-slate-500">Git repository bağlayarak ilk uygulamanızı birkaç adımda hazırlayabilirsiniz.</p>
            <a href="<?= BASE_URL ?>/uygulamalar/olustur" class="mt-6 inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">İlk Uygulamayı Oluştur</a>
        </div>
    <?php else: ?>
        <div class="grid gap-5 xl:grid-cols-2">
            <?php foreach ($uygulamalar as $uygulama): ?>
                <?php
                $durumSinifi = match ($uygulama['durum']) {
                    'running' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                    'deploying' => 'border-blue-200 bg-blue-50 text-blue-700',
                    'error' => 'border-red-200 bg-red-50 text-red-700',
                    'suspended' => 'border-amber-200 bg-amber-50 text-amber-700',
                    'stopped' => 'border-slate-300 bg-slate-100 text-slate-700',
                    default => 'border-violet-200 bg-violet-50 text-violet-700',
                };
                $calisiyor = $uygulama['durum'] === 'running';
                ?>
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex min-w-0 items-center gap-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-lg text-white"><i class="fa-solid fa-code"></i></div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate text-lg font-bold text-slate-900"><?= htmlspecialchars($uygulama['ad']) ?></h3>
                                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold <?= $durumSinifi ?>"><?= htmlspecialchars($uygulama['durum']) ?></span>
                                    </div>
                                    <p class="mt-1 truncate text-xs text-slate-400"><?= htmlspecialchars($uygulama['uygulama_adi'] ?: 'Dokploy uygulama adı bekleniyor') ?></p>
                                </div>
                            </div>
                            <span class="w-fit rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold uppercase text-slate-700"><?= htmlspecialchars($uygulama['platform']) ?></span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Proje</p><p class="mt-1 truncate text-sm font-semibold text-slate-700"><?= htmlspecialchars($uygulama['proje_adi']) ?></p></div>
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kaynak</p><p class="mt-1 text-sm font-semibold text-slate-700"><?= htmlspecialchars(strtoupper($uygulama['kaynak_tipi'])) ?></p></div>
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Branch</p><p class="mt-1 truncate text-sm font-semibold text-slate-700"><?= htmlspecialchars($uygulama['git_dal'] ?: '-') ?></p></div>
                        </div>

                        <?php if (!empty($uygulama['son_hata'])): ?>
                            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700"><?= htmlspecialchars($uygulama['son_hata']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <a href="<?= BASE_URL ?>/uygulamalar/domainler?id=<?= (int) $uygulama['id'] ?>" class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700"><i class="fa-solid fa-globe mr-2"></i>Domainler</a>
                        <form action="<?= BASE_URL ?>/uygulamalar/deploy" method="POST"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="id" value="<?= (int) $uygulama['id'] ?>"><button type="submit" class="inline-flex h-9 items-center rounded-lg bg-blue-600 px-3 text-xs font-semibold text-white hover:bg-blue-700">Deploy</button></form>
                        <form action="<?= BASE_URL ?>/uygulamalar/yeniden-deploy" method="POST"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="id" value="<?= (int) $uygulama['id'] ?>"><button type="submit" class="inline-flex h-9 items-center rounded-lg bg-slate-900 px-3 text-xs font-semibold text-white hover:bg-slate-700">Redeploy</button></form>
                        <?php if ($calisiyor): ?>
                            <form action="<?= BASE_URL ?>/uygulamalar/durdur" method="POST"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="id" value="<?= (int) $uygulama['id'] ?>"><button type="submit" class="inline-flex h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-semibold text-amber-700 hover:bg-amber-100">Durdur</button></form>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>/uygulamalar/baslat" method="POST"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="id" value="<?= (int) $uygulama['id'] ?>"><button type="submit" class="inline-flex h-9 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Başlat</button></form>
                        <?php endif; ?>
                        <form action="<?= BASE_URL ?>/uygulamalar/senkronize-et" method="POST"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="id" value="<?= (int) $uygulama['id'] ?>"><button type="submit" class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 hover:bg-slate-100"><i class="fa-solid fa-rotate mr-2"></i>Senkronize</button></form>
                        <form action="<?= BASE_URL ?>/uygulamalar/sil" method="POST" class="ml-auto" onsubmit="return confirm('Bu uygulama Dokploy üzerinden de silinecek. Devam edilsin mi?')"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="id" value="<?= (int) $uygulama['id'] ?>"><button type="submit" class="inline-flex h-9 items-center rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100"><i class="fa-solid fa-trash mr-2"></i>Sil</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = 'Uygulamalar';
require __DIR__ . '/../layouts/app.php';
?>