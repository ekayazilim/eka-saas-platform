<?php ob_start(); ?>

<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/admin/plans" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-900"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Cloud Paketini Düzenle</h2>
            <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars($plan['name']) ?> kaynak ve özellik limitlerini güncelleyin.</p>
        </div>
    </div>

    <form action="<?= BASE_URL ?>/admin/plans/update" method="POST" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="id" value="<?= (int) $plan['id'] ?>">

        <div class="border-b border-slate-100 p-6 sm:p-8">
            <h3 class="text-lg font-bold text-slate-900">Paket Bilgileri</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Paket Adı</label><input type="text" name="name" required maxlength="255" value="<?= htmlspecialchars($plan['name']) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Aylık Ücret (₺)</label><input type="number" name="price" required min="0" step="0.01" value="<?= htmlspecialchars((string) $plan['price']) ?>" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
            </div>
        </div>

        <div class="border-b border-slate-100 p-6 sm:p-8">
            <h3 class="text-lg font-bold text-slate-900">Kota Limitleri</h3>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Kullanıcı</label><input type="number" name="user_limit" min="1" value="<?= (int) $plan['user_limit'] ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Proje</label><input type="number" name="project_limit" min="1" value="<?= (int) $plan['project_limit'] ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Uygulama</label><input type="number" name="application_limit" min="1" value="<?= (int) ($plan['application_limit'] ?? 1) ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Domain</label><input type="number" name="domain_limit" min="0" value="<?= (int) ($plan['domain_limit'] ?? 1) ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Veritabanı</label><input type="number" name="database_limit" min="0" value="<?= (int) ($plan['database_limit'] ?? 0) ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">API İsteği</label><input type="number" name="api_limit" min="0" value="<?= (int) $plan['api_limit'] ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
            </div>
        </div>

        <div class="border-b border-slate-100 p-6 sm:p-8">
            <h3 class="text-lg font-bold text-slate-900">Kaynak Limitleri</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-3">
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">RAM (MB)</label><input type="number" name="memory_limit_mb" min="128" value="<?= (int) ($plan['memory_limit_mb'] ?? 512) ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">CPU (millicore)</label><input type="number" name="cpu_limit_millicores" min="100" value="<?= (int) ($plan['cpu_limit_millicores'] ?? 500) ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"><p class="mt-1 text-xs text-slate-400">1000 = 1 vCPU</p></div>
                <div><label class="mb-2 block text-sm font-semibold text-slate-700">Disk (MB)</label><input type="number" name="storage_limit_mb" min="512" value="<?= (int) ($plan['storage_limit_mb'] ?? 5120) ?>" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-slate-900">Özellik İzinleri</h3>
            <div class="mt-5 grid gap-3 md:grid-cols-3">
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4"><input type="checkbox" name="allow_docker" value="1" <?= !empty($plan['allow_docker']) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300"><span class="text-sm font-semibold text-slate-700">Docker kullanımı</span></label>
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4"><input type="checkbox" name="allow_databases" value="1" <?= !empty($plan['allow_databases']) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300"><span class="text-sm font-semibold text-slate-700">Veritabanı servisi</span></label>
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4"><input type="checkbox" name="allow_custom_domain" value="1" <?= !empty($plan['allow_custom_domain']) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300"><span class="text-sm font-semibold text-slate-700">Özel domain</span></label>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:px-8">
            <a href="<?= BASE_URL ?>/admin/plans" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700">İptal</a>
            <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Paketi Kaydet</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Cloud Paketini Düzenle';
require __DIR__ . '/../../layouts/admin.php';
?>