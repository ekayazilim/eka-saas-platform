<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaAuth;
use Core\EkaValidator;
use App\Models\EkaUser;
use App\Models\EkaTenantModel;
use App\Models\EkaActivityLog;

class EkaAuthController extends EkaController
{
    public function showLogin()
    {
        return $this->view('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $validator = new EkaValidator();
        if (!$validator->validate($_POST, ['email' => 'required|email', 'password' => 'required'])) {
            $_SESSION['error'] = $validator->firstError();
            return $this->redirect('/login');
        }

        $userModel = new EkaUser();
        $user = $userModel->first('email', $email);

        if ($user && password_verify($password, $user['password'])) {
            EkaAuth::login($user);
            
            if ($user['tenant_id']) {
                $tenantModel = new EkaTenantModel();
                $tenant = $tenantModel->find($user['tenant_id']);
                if ($tenant) {
                    \Core\EkaTenant::set($tenant);
                }
            }

            if ($user['tenant_id']) {
                (new EkaActivityLog())->log($user['tenant_id'], $user['id'], 'user_login', 'Kullanıcı giriş yaptı.');
            }

            if ($user['role'] === 'super_admin') {
                return $this->redirect('/admin/dashboard');
            }

            return $this->redirect('/dashboard');
        }

        $_SESSION['error'] = 'E-posta veya şifre hatalı.';
        return $this->redirect('/login');
    }

    public function showRegister()
    {
        return $this->view('auth/register');
    }

    public function register()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $companyName = $_POST['company_name'] ?? '';

        $validator = new EkaValidator();
        if (!$validator->validate($_POST, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'company_name' => 'required'
        ])) {
            $_SESSION['error'] = $validator->firstError();
            return $this->redirect('/register');
        }

        $userModel = new EkaUser();
        if ($userModel->first('email', $email)) {
            $_SESSION['error'] = 'Bu e-posta adresi zaten kullanılıyor.';
            return $this->redirect('/register');
        }

        $tenantModel = new EkaTenantModel();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $companyName)));
        
        $tenantId = $tenantModel->create([
            'name' => $companyName,
            'slug' => $slug,
            'plan_id' => 1,
            'status' => 'active'
        ]);

        $userId = $userModel->create([
            'tenant_id' => $tenantId,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'owner'
        ]);

        (new EkaActivityLog())->log($tenantId, $userId, 'tenant_registered', 'Yeni firma kaydı yapıldı.');

        $_SESSION['success'] = 'Kayıt başarılı, lütfen giriş yapın.';
        return $this->redirect('/login');
    }

    public function logout()
    {
        if (EkaAuth::check() && \Core\EkaTenant::id()) {
            (new EkaActivityLog())->log(\Core\EkaTenant::id(), EkaAuth::id(), 'user_logout', 'Kullanıcı çıkış yaptı.');
        }
        
        EkaAuth::logout();
        \Core\EkaTenant::clear();
        return $this->redirect('/login');
    }

    public function showForgot()
    {
        return $this->view('auth/forgot-password');
    }
}
