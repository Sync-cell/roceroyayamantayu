<?php


namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'customer_id',
        'admin_id',
        'type',
        'title',
        'message',
        'related_id',
        'meta',
        'is_read',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ============ CUSTOMER NOTIFICATIONS ============
    public function getUnreadCustomer($customerId, $limit = 10)
    {
        return $this->where('customer_id', $customerId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getAllCustomer($customerId, $limit = null)
    {
        $query = $this->where('customer_id', $customerId)
                      ->orderBy('created_at', 'DESC');
        if ($limit) $query->limit($limit);
        return $query->findAll();
    }

    public function countUnreadCustomer($customerId)
    {
        return $this->where('customer_id', $customerId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    // ============ ADMIN NOTIFICATIONS ============
    public function getUnreadAdmin($adminId, $limit = 10)
    {
        return $this->where('admin_id', $adminId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getAllAdmin($adminId, $limit = null)
    {
        $query = $this->where('admin_id', $adminId)
                      ->orderBy('created_at', 'DESC');
        if ($limit) $query->limit($limit);
        return $query->findAll();
    }

    public function countUnreadAdmin($adminId)
    {
        return $this->where('admin_id', $adminId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    // ============ MARK AS READ ============
    public function markAsRead($id)
    {
        return $this->update($id, ['is_read' => 1]);
    }

    public function markAllAsReadCustomer($customerId)
    {
        return $this->where('customer_id', $customerId)
                    ->set(['is_read' => 1])
                    ->update();
    }

    public function markAllAsReadAdmin($adminId)
    {
        return $this->where('admin_id', $adminId)
                    ->set(['is_read' => 1])
                    ->update();
    }

    // ============ CREATE NOTIFICATIONS ============
    /**
     * Create a notification for customer
     *
     * @param int $customerId
     * @param string $title
     * @param string $message
     * @param string|null $type
     * @param string|null $relatedId
     * @param array|null $meta
     * @return int|false
     */
    public function createCustomerNotification(
        int $customerId,
        string $title,
        string $message,
        ?string $type = null,
        ?string $relatedId = null,
        ?array $meta = null
    ) {
        $data = [
            'customer_id' => $customerId,
            'admin_id'    => null,
            'type'        => $type,
            'title'       => $title,
            'message'     => $message,
            'related_id'  => $relatedId,
            'meta'        => $meta ? json_encode($meta) : null,
            'is_read'     => 0,
        ];

        if ($this->insert($data) === false) {
            return false;
        }

        return (int) $this->getInsertID();
    }

    /**
     * Create a notification for admin
     *
     * @param int $adminId
     * @param string $title
     * @param string $message
     * @param string|null $type
     * @param string|null $relatedId
     * @param array|null $meta
     * @return int|false
     */
    public function createAdminNotification(
        int $adminId,
        string $title,
        string $message,
        ?string $type = null,
        ?string $relatedId = null,
        ?array $meta = null
    ) {
        $data = [
            'customer_id' => null,
            'admin_id'    => $adminId,
            'type'        => $type,
            'title'       => $title,
            'message'     => $message,
            'related_id'  => $relatedId,
            'meta'        => $meta ? json_encode($meta) : null,
            'is_read'     => 0,
        ];

        if ($this->insert($data) === false) {
            return false;
        }

        return (int) $this->getInsertID();
    }

    // ============ FIND NOTIFICATIONS ============
    /**
     * Find notification by ID (with ownership check)
     *
     * @param int $id
     * @param int|null $customerId
     * @param int|null $adminId
     * @return array|null
     */
    public function findNotification(int $id, ?int $customerId = null, ?int $adminId = null)
    {
        $query = $this->where('id', $id);
        
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        } elseif ($adminId !== null) {
            $query->where('admin_id', $adminId);
        }

        return $query->first();
    }

    /**
     * Get notifications by type
     */
    public function getByType(string $type, ?int $customerId = null, ?int $adminId = null)
    {
        $query = $this->where('type', $type);
        
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        } elseif ($adminId !== null) {
            $query->where('admin_id', $adminId);
        }

        return $query->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get notifications by related ID
     */
    public function getByRelatedId($relatedId, ?int $customerId = null, ?int $adminId = null)
    {
        $query = $this->where('related_id', $relatedId);
        
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        } elseif ($adminId !== null) {
            $query->where('admin_id', $adminId);
        }

        return $query->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Delete notification
     */
    public function deleteNotification(int $id, ?int $customerId = null, ?int $adminId = null)
    {
        $notif = $this->findNotification($id, $customerId, $adminId);
        if (!$notif) return false;
        return $this->delete($id);
    }

    /**
     * Get notification stats
     */
    public function getStats(?int $customerId = null, ?int $adminId = null)
    {
        $query = $this->builder();
        
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        } elseif ($adminId !== null) {
            $query->where('admin_id', $adminId);
        }

        return [
            'total' => (clone $query)->countAllResults(),
            'unread' => (clone $query)->where('is_read', 0)->countAllResults(),
            'read' => (clone $query)->where('is_read', 1)->countAllResults(),
        ];
    }
}