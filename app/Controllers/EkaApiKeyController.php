<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaTenant;
use App\Models\EkaApiKey;
use App\Models\EkaActivityLog;
use Core\EkaAuth;

class EkaApiKeyController extends EkaController
{
    public function index()
    {
        $apiKeyModel = new EkaApiKey();
        $keys = $apiKeyModel->where('tenant_id', EkaTenant::id());
        
        return $this->view('api-keys/index', ['keys' => $keys]);
    }

    public function generate()
    {
        $name = $_POST['name'] ?? 'Yeni API Anahtarı';
        $keyValue = 'ek_live_' . bin2hex(random_bytes(16));

        $apiKeyModel = new EkaApiKey();
        $apiKeyModel->create([
            'tenant_id' => EkaTenant::id(),
            'name' => $name,
            'key_value' => $keyValue
        ]);

        (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'api_key_generated', "API anahtarı oluşturuldu: {$name}");

        $_SESSION['success'] = 'API anahtarı başarıyla oluşturuldu.';
        return $this->redirect('/api-keys');
    }

    public function revoke()
    {
        $id = $_POST['id'] ?? 0;
        $apiKeyModel = new EkaApiKey();
        $key = $apiKeyModel->find($id);

        if ($key && $key['tenant_id'] === EkaTenant::id()) {
            $apiKeyModel->delete($id);
            (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'api_key_revoked', "API anahtarı iptal edildi.");
            $_SESSION['success'] = 'API anahtarı iptal edildi.';
        }

        return $this->redirect('/api-keys');
    }
}
