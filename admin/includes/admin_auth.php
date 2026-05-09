<?php
/**
 * Admin authentication & page-level permission helper.
 *
 * Usage at the top of every protected admin page:
 *
 *   require_once __DIR__ . '/includes/admin_auth.php';
 *   requireAdminLogin();
 *   requirePermission('series'); // optional, only on permission-gated pages
 *
 * Permission keys are catalog rows in `admin_permissions` table.
 */

// NOTE: We intentionally do NOT call session_start() here.
// Every admin entry page calls session_start() itself, and calling it again
// here would raise a "session already started" warning. The helper functions
// below assume the session has been started by the caller before they are
// invoked (which is the convention in every admin page).

require_once __DIR__ . '/../../classes/connect.php';

if (!function_exists('admin_auth_pdo')) {
    function admin_auth_pdo() {
        return Database::getPdoConnection();
    }
}

if (!function_exists('requireAdminLogin')) {
    function requireAdminLogin() {
        if (empty($_SESSION['admin_id'])) {
            header('location: login.php');
            exit;
        }

        // If account was deactivated mid-session, force logout.
        try {
            $pdo = admin_auth_pdo();
            $stmt = $pdo->prepare("SELECT is_active FROM `admin` WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || (int)$row['is_active'] !== 1) {
                $_SESSION = [];
                session_destroy();
                header('location: login.php?inactive=1');
                exit;
            }
        } catch (Exception $e) {
            // If DB check fails, fail closed.
            $_SESSION = [];
            session_destroy();
            header('location: login.php');
            exit;
        }
    }
}

if (!function_exists('loadAdminPermissions')) {
    /**
     * Load and cache the current admin's permissions in session.
     * Returns array of permission keys.
     */
    function loadAdminPermissions($force = false) {
        if (empty($_SESSION['admin_id'])) {
            return [];
        }

        if (!$force && isset($_SESSION['admin_permissions']) && is_array($_SESSION['admin_permissions'])) {
            return $_SESSION['admin_permissions'];
        }

        try {
            $pdo = admin_auth_pdo();
            $stmt = $pdo->prepare("
                SELECT p.permission_key
                FROM admin_permission_map m
                INNER JOIN admin_permissions p ON p.id = m.permission_id
                WHERE m.admin_id = ? AND p.is_active = 1
            ");
            $stmt->execute([$_SESSION['admin_id']]);
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $permissions = $rows ? array_values(array_unique($rows)) : [];
        } catch (Exception $e) {
            $permissions = [];
        }

        $_SESSION['admin_permissions'] = $permissions;
        return $permissions;
    }
}

if (!function_exists('adminHasPermission')) {
    function adminHasPermission($permission_key) {
        if (empty($_SESSION['admin_id'])) {
            return false;
        }
        $permissions = loadAdminPermissions();
        return in_array($permission_key, $permissions, true);
    }
}

if (!function_exists('adminHasAnyPermission')) {
    function adminHasAnyPermission(array $permission_keys) {
        foreach ($permission_keys as $key) {
            if (adminHasPermission($key)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('requirePermission')) {
    function requirePermission($permission_key) {
        requireAdminLogin();
        if (!adminHasPermission($permission_key)) {
            http_response_code(403);
            include __DIR__ . '/admin_forbidden.php';
            exit;
        }
    }
}

if (!function_exists('clearAdminPermissionCache')) {
    function clearAdminPermissionCache() {
        unset($_SESSION['admin_permissions']);
    }
}

if (!function_exists('getAllAdminPermissions')) {
    /**
     * Return the full permission catalog (id, key, label, description).
     */
    function getAllAdminPermissions() {
        try {
            $pdo = admin_auth_pdo();
            $stmt = $pdo->prepare("SELECT id, permission_key, label, description FROM admin_permissions WHERE is_active = 1 ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('getAdminPermissionIds')) {
    /**
     * Return the set of permission IDs granted to a given admin.
     */
    function getAdminPermissionIds($admin_id) {
        try {
            $pdo = admin_auth_pdo();
            $stmt = $pdo->prepare("SELECT permission_id FROM admin_permission_map WHERE admin_id = ?");
            $stmt->execute([$admin_id]);
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $ids ? array_map('intval', $ids) : [];
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('saveAdminPermissions')) {
    /**
     * Replace the permission set of $admin_id with the provided permission IDs.
     */
    function saveAdminPermissions($admin_id, array $permission_ids) {
        $admin_id = (int)$admin_id;
        if ($admin_id <= 0) {
            return false;
        }

        $permission_ids = array_values(array_unique(array_map('intval', $permission_ids)));
        $permission_ids = array_filter($permission_ids, function ($v) { return $v > 0; });

        try {
            $pdo = admin_auth_pdo();
            $pdo->beginTransaction();

            $del = $pdo->prepare("DELETE FROM admin_permission_map WHERE admin_id = ?");
            $del->execute([$admin_id]);

            if (!empty($permission_ids)) {
                $ins = $pdo->prepare("INSERT INTO admin_permission_map (admin_id, permission_id) VALUES (?, ?)");
                foreach ($permission_ids as $pid) {
                    $ins->execute([$admin_id, $pid]);
                }
            }

            $pdo->commit();

            // If updating self, refresh session cache immediately.
            if (!empty($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $admin_id) {
                clearAdminPermissionCache();
                loadAdminPermissions(true);
            }

            return true;
        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }
}
