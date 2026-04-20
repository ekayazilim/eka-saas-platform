<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaTenant;
use App\Models\EkaNotification;

class EkaNotificationController extends EkaController
{
    public function index()
    {
        $notificationModel = new EkaNotification();
        $notifications = $notificationModel->where('tenant_id', EkaTenant::id());
        
        return $this->view('notifications/index', ['notifications' => $notifications]);
    }
    
    public function read()
    {
        $id = $_POST['id'] ?? 0;
        $notificationModel = new EkaNotification();
        $notif = $notificationModel->find($id);
        
        if ($notif && $notif['tenant_id'] === EkaTenant::id()) {
            $notificationModel->update($id, ['is_read' => 1]);
        }
        
        return $this->redirect('/notifications');
    }
}
