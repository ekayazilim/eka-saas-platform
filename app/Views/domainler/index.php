<?php ob_start(); ?>

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="<?= BASE_URL ?>/uygulamalar" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-900"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Domain Yönetimi</h2>
                <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars($uygulama['ad']) ?> için özel domain ve SSL yapılandırması.</p>
            </div>
        </div>
        <span class="inline-flex w-fit rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600"><?= (int) $domainSayisi ?> / <?= (int) $domainLimiti ?> domain</span>
    </div>

    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-blue-900">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-circle-info mt-0.5"></i>
            <div class="text-sm">
                <p class="font-semibold">SSL oluşturmadan önce domain DNS kaydını deployment sunucusunun IP adresine yönlendirin.</p>
                <p class="mt-1 text-blue-800">React/Vite ve statik uygulamalarda container port genellikle 80, Next.js ve Node.js uygulamalarında çoğunlukla 3000 kullanılır.</p>
            </div>
        </div>
    </div>

    <?php if ($ozelDomainKullanabilir): ?>
        <form action="<?= BASE_URL ?>/uygulamalar/domainler/kaydet" method="POST" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <input type="hidden" name="uygulama_id" value="<?= (int) $uygulama['id'] ?>">
            <h3 class="text-lg font-bold text-slate-900">Yeni Domain Ekle</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-[1fr_160px_auto] md:items-end">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Domain</label>
                    <input type="text" name="host" required maxlength="253" placeholder="app.ornek.com" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Container Port</label>
                    <input type="number" name="port" required min="1" max="65535" value="<?= (int) $varsayilanPort ?>" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
                <label class="flex h-[46px] cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700"><input type="checkbox" name="https" value="1" checked class="h-4 w-4 rounded border-slate-300"> HTTPS + SSL</label>
            </div>
            <div class="mt-5 flex justify-end">
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"><i class="fa-solid fa-globe mr-2"></i>Domain Ekle</button>
            </div>
        </form>
    <?php else: ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">Mevcut paketiniz özel domain kullanımına izin vermiyor.</div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5"><h3 class="font-bold text-slate-900">Bağlı Domainler</h3></div>
        <?php if (empty($domainler)): ?>
            <div class="px-6 py-12 text-center text-sm text-slate-500">Henüz bu uygulamaya domain eklenmedi.</div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($domainler as $domain): ?>
                    <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600"><i class="fa-solid fa-link"></i></div>
                            <div>
                                <a href="<?= $domain['https'] ? 'https://' : 'http://' ?><?= htmlspecialchars($domain['host']) ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-slate-900 hover:text-blue-700"><?= htmlspecialchars($domain['host']) ?></a>
                                <p class="mt-1 text-xs text-slate-500">Port <?= (int) $domain['port'] ?> · <?= $domain['https'] ? 'Let’s Encrypt SSL' : 'HTTP' ?></p>
                            </div>
                        </div>
                        <form action="<?= BASE_URL ?>/uygulamalar/domainler/sil" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" value="<?= (int) $domain['id'] ?>">
                            <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-semibold text-red-700 hover:bg-red-100">Domaini Kaldır</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Domain Yönetimi';
require __DIR__ . '/../layouts/app.php';
?>