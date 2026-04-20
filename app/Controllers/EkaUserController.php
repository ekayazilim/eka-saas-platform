<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaTenant;
use App\Models\EkaUser;
use App\Models\EkaActivityLog;
use Core\EkaAuth;

class EkaUserController extends EkaController
{
    public function index()
    {
        $userModel = new EkaUser();
        $users = $userModel->where('tenant_id', EkaTenant::id());
        
        return $this->view('users/index', ['users' => $users]);
    }

    public function create()
    {
        return $this->view('users/create');
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';

        $userModel = new EkaUser();
        if ($userModel->first('email', $email)) {
            $_SESSION['error'] = 'Bu e-posta adresi kullanımda.';
            return $this->redirect('/users/create');
        }

        $userModel->create([
            'tenant_id' => EkaTenant::id(),
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role
        ]);

        (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'member_invited', "Yeni üye eklendi: {$email}");

        $_SESSION['success'] = 'Kullanıcı başarıyla eklendi.';
        return $this->redirect('/users');
    }

    public function delete()
    {
        $id = $_POST['id'] ?? 0;
        $userModel = new EkaUser();
        $user = $userModel->find($id);

        if ($user && $user['tenant_id'] === EkaTenant::id() && $user['id'] !== EkaAuth::id()) {
            $userModel->delete($id);
            (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'member_removed', "Üye silindi.");
            $_SESSION['success'] = 'Kullanıcı başarıyla silindi.';
        } else {
            $_SESSION['error'] = 'Kullanıcı silinemedi.';
        }

        return $this->redirect('/users');
    }
}
