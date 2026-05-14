<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\UserService;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Helpers\Response;

class SuperadminController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    /**
     * Show superadmin dashboard.
     *
     * Combines admin-level system stats with superadmin-only shortcuts
     * (user role management, deep user controls) and surfaces parent/doctor
     * sections that superadmin can also browse into.
     */
    public function dashboard(): void
    {
        $stats = $this->userService->getAdminStats();
        $notificationModel = new Notification();
        $notifications = $notificationModel->getUnread($this->userId(), 10);
        $unreadCount = $notificationModel->getUnreadCount($this->userId());

        $activityLog = new ActivityLog();
        $recentActivity = $activityLog->getRecent(15);

        $this->view('superadmin/dashboard', [
            'user' => $this->currentUser(),
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * User management page — includes all user types and role-change controls.
     */
    public function users(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $filters = [
            'search' => $_GET['search'] ?? '',
            'user_type' => $_GET['user_type'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];

        $result = $this->userService->getUsers($filters, $page);

        if ($this->isAjax()) {
            Response::success($result);
            return;
        }

        $this->view('superadmin/users', [
            'user' => $this->currentUser(),
            'users' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
        ]);
    }

    /**
     * Change a user's role. Doctors cannot have their role changed and
     * no user can be promoted *into* a doctor role from here.
     */
    public function changeRole(): void
    {
        $userId = (int) $this->input('user_id');
        $newRole = (string) $this->input('user_type');

        $result = $this->userService->changeUserRole($userId, $newRole, (int) $this->userId());
        $result['success'] ? Response::success(null, $result['message']) : Response::error($result['message']);
    }

    /**
     * Toggle user status — same flow as admin, but exposed under /superadmin.
     */
    public function toggleStatus(): void
    {
        $userId = (int) $this->input('user_id');
        $status = (string) $this->input('status');

        $result = $this->userService->toggleStatus($userId, $status, (int) $this->userId());
        $result['success'] ? Response::success(null, $result['message']) : Response::error($result['message']);
    }

    /**
     * Delete a user. Doctors cannot be deleted here.
     */
    public function deleteUser(): void
    {
        $userId = (int) $this->input('user_id');

        $result = $this->userService->deleteUser($userId, (int) $this->userId());
        $result['success'] ? Response::success(null, $result['message']) : Response::error($result['message']);
    }
}
