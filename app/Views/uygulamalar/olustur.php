<?php ob_start(); ?>

<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/uygulamalar" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Yeni Uygulama</h2>
            <p class="mt-1 text-sm text-slate-500">Kaynak kodunuzu deployment altyapısına bağlayın ve uygulamanızı hazırlayın.</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Uygulama Hakkı</p>
            <p class="mt-1 text-lg font-bold text-slate-900"><?= (int) $limitler['application_limit'] ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">RAM</p>
            <p class="mt-1 text-lg font-bold text-slate-900"><?= number_format((int) $limitler['memory_limit_mb'] / 1024, 1) ?> GB</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">CPU</p>
            <p class="mt-1 text-lg font-bold text-slate-900"><?= number_format((int) $limitler['cpu_limit_millicores'] / 1000, 2) ?> vCPU</p>
        </div>
    </div>

    <?php if (empty($projeler)): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p class="font-semibold">Önce bir proje oluşturmalısınız.</p>
            <p class="mt-1 text-sm">Her uygulama tenant içindeki bir projeye bağlı çalışır.</p>
            <a href="<?= BASE_URL ?>/projects/create" class="mt-4 inline-flex rounded-xl bg-amber-900 px-4 py-2.5 text-sm font-semibold text-white">Proje Oluştur</a>
        </div>
    <?php else: ?>
        <form action="<?= BASE_URL ?>/uygulamalar/kaydet" method="POST" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="border-b border-slate-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-slate-900">Uygulama Bilgileri</h3>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Uygulama Adı</label>
                        <input type="text" name="ad" required maxlength="191" placeholder="Örn: Müşteri Portalı" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Proje</label>
                        <select name="project_id" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="">Proje seçiniz</option>
                            <?php foreach ($projeler as $proje): ?>
                                <option value="<?= (int) $proje['id'] ?>"><?= htmlspecialchars($proje['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Platform</label>
                        <select name="platform" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="react">React / Vite</option>
                            <option value="nextjs">Next.js</option>
                            <option value="node">Node.js</option>
                            <option value="python">Python</option>
                            <option value="static">Statik Site</option>
                            <?php if ($dockerKullanabilir): ?><option value="docker">Dockerfile</option><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Kaynak Tipi</label>
                        <select name="kaynak_tipi" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="git">Git URL</option>
                            <option value="github">Bağlı GitHub Sağlayıcısı</option>
                            <?php if ($dockerKullanabilir): ?><option value="docker">Docker Image</option><?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-slate-900">Git Kaynağı</h3>
                <p class="mt-1 text-sm text-slate-500">Public GitHub, GitLab veya başka bir Git adresi için yalnızca Git URL alanını doldurabilirsiniz.</p>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Git Repository URL</label>
                        <input type="url" name="git_url" placeholder="https://github.com/kullanici/proje.git" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Branch</label>
                        <input type="text" name="git_dal" value="main" maxlength="191" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Build Yolu</label>
                        <input type="text" name="git_build_yolu" value="/" maxlength="191" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-100 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-slate-900">Bağlı GitHub Sağlayıcısı</h3>
                <p class="mt-1 text-sm text-slate-500">Private repository kullanacaksanız Dokploy tarafında tanımlı GitHub sağlayıcısının kimliğini kullanın.</p>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">GitHub Provider ID</label>
                        <input type="text" name="github_id" maxlength="191" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Repository Sahibi</label>
                        <input type="text" name="git_sahip" maxlength="191" placeholder="ekayazilim" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Repository</label>
                        <input type="text" name="git_repo" maxlength="191" placeholder="uygulama" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </div>

            <?php if ($dockerKullanabilir): ?>
                <div class="border-b border-slate-100 p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-slate-900">Docker Image</h3>
                    <div class="mt-6">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Image</label>
                        <input type="text" name="docker_image" maxlength="255" placeholder="nginx:latest" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            <?php endif; ?>

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-slate-900">Environment Variables</h3>
                <p class="mt-1 text-sm text-slate-500">Değerler Eka veritabanına kaydedilmez; doğrudan deployment altyapısına gönderilir.</p>
                <textarea name="env" rows="7" spellcheck="false" placeholder="NODE_ENV=production&#10;API_URL=https://api.example.com" class="mt-5 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:flex-row sm:justify-end sm:px-8">
                <a href="<?= BASE_URL ?>/uygulamalar" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">İptal</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Uygulamayı Hazırla
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = 'Yeni Uygulama';
require __DIR__ . '/../layouts/app.php';
?>