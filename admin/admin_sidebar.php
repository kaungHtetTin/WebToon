<?php
// admin_header.php is expected to be included before this file, which loads
// the admin permission helpers and validates the session.
if (!function_exists('adminHasPermission')) {
    require_once __DIR__ . '/includes/admin_auth.php';
    requireAdminLogin();
    loadAdminPermissions();
}
?>
  <!-- ======= Sidebar - Modern Drawer Navigation Style ======= -->
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link" href="index.php" data-page="index">
          <i class="bi bi-speedometer2"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <?php if (adminHasPermission('analytics')): ?>
      <li class="nav-item">
        <a class="nav-link" href="analytics.php" data-page="analytics">
          <i class="bi bi-graph-up-arrow"></i>
          <span>Analytics</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasAnyPermission(['categories','series','chapters','contents','owl_carousels'])): ?>
      <li class="nav-heading">Content Management</li>

      <?php if (adminHasPermission('categories')): ?>
      <li class="nav-item">
        <a class="nav-link" href="categories.php" data-page="categories">
          <i class="bi bi-tags"></i>
          <span>Categories</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('series')): ?>
      <li class="nav-item">
        <a class="nav-link" href="series.php" data-page="series">
          <i class="bi bi-book"></i>
          <span>Series</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('owl_carousels')): ?>
      <li class="nav-item">
        <a class="nav-link" href="owl_carousels.php" data-page="owl_carousels">
          <i class="bi bi-images"></i>
          <span>Carousels</span>
        </a>
      </li>
      <?php endif; ?>
      <?php endif; ?>

      <?php if (adminHasAnyPermission(['blogs','blog_feeds'])): ?>
      <li class="nav-heading">Blog Management</li>

      <?php if (adminHasPermission('blogs')): ?>
      <li class="nav-item">
        <a class="nav-link" href="blogs.php" data-page="blogs">
          <i class="bi bi-journal-text"></i>
          <span>Blogs</span>
        </a>
      </li>
      <?php endif; ?>
      <?php endif; ?>

      <?php if (adminHasAnyPermission(['users','admin','payment_methods','point_prices','payments'])): ?>
      <li class="nav-heading">Users & Payments</li>

      <?php if (adminHasPermission('users')): ?>
      <li class="nav-item">
        <a class="nav-link" href="users.php" data-page="users">
          <i class="bi bi-people"></i>
          <span>Users</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('admin')): ?>
      <li class="nav-item">
        <a class="nav-link" href="admin.php" data-page="admin">
          <i class="bi bi-shield-check"></i>
          <span>Admins</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('payment_methods')): ?>
      <li class="nav-item">
        <a class="nav-link" href="payment_methods.php" data-page="payment_methods">
          <i class="bi bi-wallet2"></i>
          <span>Payment Methods</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('point_prices')): ?>
      <li class="nav-item">
        <a class="nav-link" href="point_prices.php" data-page="point_prices">
          <i class="bi bi-coin"></i>
          <span>Point Prices</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('payments')): ?>
      <li class="nav-item">
        <a class="nav-link" href="approve_payment.php" data-page="approve_payment">
          <i class="bi bi-credit-card"></i>
          <span>Payments</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="pending_payment.php" data-page="pending_payment">
          <i class="bi bi-clock-history"></i>
          <span>Pending Payments</span>
        </a>
      </li>
      <?php endif; ?>
      <?php endif; ?>

      <li class="nav-heading">Settings & Support</li>

      <li class="nav-item">
        <a class="nav-link" href="users_profile.php" data-page="users_profile">
          <i class="bi bi-person-circle"></i>
          <span>Profile</span>
        </a>
      </li>

      <?php if (adminHasPermission('api_documentation')): ?>
      <li class="nav-item">
        <a class="nav-link" href="api_documentation.php" data-page="api_documentation">
          <i class="bi bi-code-slash"></i>
          <span>API Documentation</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (adminHasPermission('contact_messages')): ?>
      <li class="nav-item">
        <a class="nav-link" href="pages_contact.php" data-page="pages_contact">
          <i class="bi bi-envelope"></i>
          <span>Contact Messages</span>
        </a>
      </li>
      <?php endif; ?>

    </ul>

  </aside>

  <!-- End Sidebar-->
