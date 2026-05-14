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

    /**
     * Edit a user's basic profile fields (name/email/phone/role/status).
     */
    public function updateUser(int $id): void
    {
        $data = [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'user_type' => $this->input('user_type'),
            'status' => $this->input('status'),
        ];

        $result = $this->userService->updateUserAsSuperadmin($id, $data, (int) $this->userId());
        $result['success'] ? Response::success(null, $result['message']) : Response::error($result['message']);
    }

    /**
     * Show superadmin appointments page — same controls as the admin
     * booking flow but reachable under the superadmin sidebar.
     */
    public function appointments(): void
    {
        $filters = [
            'doctor_id' => $_GET['doctor_id'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];
        $page = (int) ($_GET['page'] ?? 1);

        $appointmentService = new \App\Services\AppointmentService();
        $result = $appointmentService->getFiltered($filters, $page);

        $userModel = new \App\Models\User();
        $doctors = $userModel->getDoctors();

        $this->view('superadmin/appointments', [
            'user' => $this->currentUser(),
            'appointments' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Create an appointment (superadmin-driven, on behalf of any patient).
     */
    public function createAppointment(): void
    {
        $validation = $this->validate([
            'patient_id' => 'required|numeric',
            'doctor_id' => 'required|numeric',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        if (!empty($validation['errors'])) {
            Response::validationError($validation['errors']);
            return;
        }

        $data = $validation['data'];
        $data['type'] = $this->input('type', 'CONSULTATION');
        $data['reason'] = $this->input('reason');
        $data['duration'] = (int) $this->input('duration', '30');

        $appointmentService = new \App\Services\AppointmentService();
        $result = $appointmentService->bookAppointment($data, (int) $this->userId());

        $result['success'] ? Response::success($result['data'] ?? null, $result['message']) : Response::error($result['message']);
    }

    /**
     * Show "all children" page — every patient in the system.
     */
    public function children(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 20;
        $search = trim((string) ($_GET['search'] ?? ''));

        $where = '1=1';
        $params = [];
        if ($search !== '') {
            $where .= " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $s = "%{$search}%";
            $params = [$s, $s, $s, $s, $s];
        }

        $offset = ($page - 1) * $perPage;
        $children = $this->db->fetchAll(
            "SELECT p.id, p.first_name, p.last_name, p.date_of_birth, p.gender, p.blood_type,
                    p.allergies, p.medical_conditions, p.created_at,
                    u.id AS parent_id, u.first_name AS parent_first_name,
                    u.last_name AS parent_last_name, u.email AS parent_email
             FROM patients p
             JOIN users u ON u.id = p.parent_id
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );
        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM patients p JOIN users u ON u.id = p.parent_id WHERE {$where}",
            $params
        );

        if ($this->isAjax()) {
            Response::success($children);
            return;
        }

        $this->view('superadmin/children', [
            'user' => $this->currentUser(),
            'children' => $children,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
            'filters' => ['search' => $search],
        ]);
    }
}
