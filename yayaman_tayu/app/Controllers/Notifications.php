<?php


namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * List all notifications for logged-in user
     */
    public function index()
    {
        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        // Determine which ID to use
        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Not authenticated');
        }

        // Determine if user is admin or customer
        $isAdmin = (bool) $adminId;

        // Build query based on user type
        if ($isAdmin) {
            // For admins, show notifications sent to admin_id
            $notifications = $this->notificationModel
                ->where('admin_id', $adminId)
                ->orderBy('created_at', 'DESC')
                ->paginate(15);
        } else {
            // For customers, show notifications sent to customer_id
            $notifications = $this->notificationModel
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'DESC')
                ->paginate(15);
        }

        $pager = $this->notificationModel->pager;

        return view('notifications/index', [
            'notifications' => $notifications,
            'pager' => $pager,
            'title' => 'Notifications',
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Get unread notifications (AJAX)
     */
    public function getUnread()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'count' => 0, 'data' => []]);
        }

        $isAdmin = (bool) $adminId;

        if ($isAdmin) {
            $query = $this->notificationModel
                ->where('admin_id', $adminId)
                ->where('is_read', 0);
        } else {
            $query = $this->notificationModel
                ->where('customer_id', $customerId)
                ->where('is_read', 0);
        }

        $count = $query->countAllResults();
        $notifications = $query->orderBy('created_at', 'DESC')->limit(10)->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'count' => $count,
            'data' => $notifications,
        ]);
    }

    /**
     * Get all notifications (AJAX)
     */
    public function getAll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'data' => []]);
        }

        $isAdmin = (bool) $adminId;

        if ($isAdmin) {
            $notifications = $this->notificationModel
                ->where('admin_id', $adminId)
                ->orderBy('created_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();
        } else {
            $notifications = $this->notificationModel
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * View single notification
     */
    public function view($id = null)
    {
        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return redirect()->to('/login');
        }

        $notification = $this->notificationModel->find($id);
        if (!$notification) {
            return redirect()->back()->with('error', 'Notification not found');
        }

        // Check ownership
        $isAdmin = (bool) $adminId;
        if ($isAdmin && $notification['admin_id'] != $adminId) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        if (!$isAdmin && $notification['customer_id'] != $customerId) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Mark as read
        $this->notificationModel->update($id, ['is_read' => 1]);

        return view('notifications/view', [
            'notification' => $notification,
            'title' => 'Notification',
        ]);
    }

    /**
     * Mark single notification as read (AJAX)
     */
    public function markAsRead($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return $this->response->setJSON(['success' => false]);
        }

        $notification = $this->notificationModel->find($id);
        if (!$notification) {
            return $this->response->setJSON(['success' => false]);
        }

        // Check ownership
        $isAdmin = (bool) $adminId;
        if ($isAdmin && $notification['admin_id'] != $adminId) {
            return $this->response->setJSON(['success' => false]);
        }
        if (!$isAdmin && $notification['customer_id'] != $customerId) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->notificationModel->update($id, ['is_read' => 1]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllAsRead()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return $this->response->setJSON(['success' => false]);
        }

        $isAdmin = (bool) $adminId;

        if ($isAdmin) {
            $this->notificationModel->where('admin_id', $adminId)->set(['is_read' => 1])->update();
        } else {
            $this->notificationModel->where('customer_id', $customerId)->set(['is_read' => 1])->update();
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function delete($id = null)
    {
        $session = session();
        $customerId = $session->get('user_id') ?? $session->get('customer_id');
        $adminId = $session->get('admin_id');

        $userId = $customerId ?? $adminId;
        if (!$userId) {
            return redirect()->to('/login');
        }

        $notification = $this->notificationModel->find($id);
        if (!$notification) {
            return redirect()->back()->with('error', 'Notification not found');
        }

        // Check ownership
        $isAdmin = (bool) $adminId;
        if ($isAdmin && $notification['admin_id'] != $adminId) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        if (!$isAdmin && $notification['customer_id'] != $customerId) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $this->notificationModel->delete($id);

        return redirect()->back()->with('success', 'Notification deleted');
    }
}