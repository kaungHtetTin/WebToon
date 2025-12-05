<?php

  include_once('config.php');
  require_once('includes/image_helper.php');

  $admin_id = $_SESSION['admin_id'];

  if(!isset($admin_id)){
     header('location:login.php');
  }

?>
  <!-- ======= Header - Firebase Console Style ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center">
      <i class="bi bi-list toggle-sidebar-btn"></i>
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">WebtoonMM</span>
      </a>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <!-- Quick Navigation Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" title="Quick Navigation">
            <i class="bi bi-grid-3x3-gap"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow quick-nav">
            <li class="dropdown-header">
              <h6>Quick Navigation</h6>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="add_categories.php">
                <i class="bi bi-plus-circle text-primary"></i>
                <span>Add Category</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="add_series.php">
                <i class="bi bi-book-plus text-primary"></i>
                <span>Add Series</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="add_chapters.php">
                <i class="bi bi-file-plus text-primary"></i>
                <span>Add Chapter</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="add_blogs.php">
                <i class="bi bi-journal-plus text-primary"></i>
                <span>Add Blog</span>
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="pending_payment.php">
                <i class="bi bi-clock-history text-warning"></i>
                <span>Pending Payments</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages_contact.php">
                <i class="bi bi-envelope text-info"></i>
                <span>Contact Messages</span>
              </a>
            </li>
          </ul>
        </li><!-- End Quick Nav -->

        <!-- Dark Mode Toggle -->
        <li class="nav-item">
          <a class="nav-link nav-icon" href="#" id="darkModeToggle" title="Toggle Dark Mode">
            <i class="bi bi-moon" id="darkModeIcon"></i>
          </a>
        </li><!-- End Dark Mode Toggle -->

        <!-- Notifications -->
        <li class="nav-item dropdown">
          <?php
            // Count pending payments
            try {
              $pending_payments = $conn->prepare("SELECT COUNT(*) as count FROM `payment_histories` WHERE (verified = 0 OR confirm = 0)");
              $pending_payments->execute();
              $pending_result = $pending_payments->fetch(PDO::FETCH_ASSOC);
              $pending_count = $pending_result ? (int)$pending_result['count'] : 0;
            } catch(Exception $e) {
              $pending_count = 0;
            }
            
            // Count recent users (try different date fields)
            $recent_count = 0;
            try {
              // Try with created_at first
              $recent_users = $conn->prepare("SELECT COUNT(*) as count FROM `users` WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
              $recent_users->execute();
              $recent_result = $recent_users->fetch(PDO::FETCH_ASSOC);
              $recent_count = $recent_result ? (int)$recent_result['count'] : 0;
            } catch(Exception $e) {
              // If created_at doesn't exist, just count all users (or skip)
              $recent_count = 0;
            }
            
            $total_notifications = $pending_count + $recent_count;
          ?>
          <a class="nav-link nav-icon position-relative" href="#" data-bs-toggle="dropdown" title="Notifications">
            <i class="bi bi-bell"></i>
            <?php if($total_notifications > 0): ?>
              <span class="badge bg-danger badge-number"><?= $total_notifications > 9 ? '9+' : $total_notifications; ?></span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              <h6>Notifications</h6>
              <?php if($total_notifications > 0): ?>
                <span class="badge bg-primary rounded-pill"><?= $total_notifications; ?> new</span>
              <?php endif; ?>
            </li>
            <li><hr class="dropdown-divider"></li>
            
            <?php if($pending_count > 0): ?>
              <li class="notification-item">
                <a href="pending_payment.php" class="d-flex align-items-center">
                  <i class="bi bi-credit-card text-warning"></i>
                  <div class="flex-grow-1">
                    <h6>Pending Payments</h6>
                    <p><?= $pending_count; ?> payment<?= $pending_count > 1 ? 's' : ''; ?> waiting for approval</p>
                    <small class="text-muted">Click to review</small>
                  </div>
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
            <?php endif; ?>
            
            <?php if($recent_count > 0): ?>
              <li class="notification-item">
                <a href="users.php" class="d-flex align-items-center">
                  <i class="bi bi-person-plus text-success"></i>
                  <div class="flex-grow-1">
                    <h6>New Users</h6>
                    <p><?= $recent_count; ?> new user<?= $recent_count > 1 ? 's' : ''; ?> registered today</p>
                    <small class="text-muted">Click to view</small>
                  </div>
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
            <?php endif; ?>
            
            <?php if($total_notifications == 0): ?>
              <li class="notification-item text-center py-4">
                <i class="bi bi-bell-slash text-muted" style="font-size: 32px;"></i>
                <p class="text-muted mb-0 mt-2">No new notifications</p>
              </li>
            <?php else: ?>
              <li class="dropdown-footer text-center">
                <a href="index.php">View all notifications</a>
              </li>
            <?php endif; ?>
          </ul>
        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown pe-3">

          <?php
            $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
          ?>

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?= htmlspecialchars(getImagePath($fetch_profile['image_url'] ?? '', 'admin')); ?>" alt="Profile" class="rounded-circle" onerror="this.src='../img/placeholder.jpg'">
            <span class="d-none d-md-block dropdown-toggle ps-2"></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?= $fetch_profile['username']; ?></h6>
              <span><?= $fetch_profile['email']; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users_profile.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header>

  <!-- End Header -->
  
  <!-- Dark Mode Script - Load early for immediate theme application -->
  <script>
    (function() {
      "use strict";
      const DARK_MODE_KEY = 'admin_dark_mode';
      const THEME_ATTRIBUTE = 'data-theme';
      
      // Initialize theme immediately (before DOM ready for faster load)
      function initTheme() {
        const savedTheme = localStorage.getItem(DARK_MODE_KEY);
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute(THEME_ATTRIBUTE, theme);
      }
      
      // Run immediately
      initTheme();
      
      // Full initialization when DOM is ready
      function initDarkMode() {
        const savedTheme = localStorage.getItem(DARK_MODE_KEY);
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (prefersDark ? 'dark' : 'light');
        
        function setTheme(theme) {
          document.documentElement.setAttribute(THEME_ATTRIBUTE, theme);
          localStorage.setItem(DARK_MODE_KEY, theme);
          updateIcon(theme);
        }
        
        function toggleTheme() {
          const currentTheme = document.documentElement.getAttribute(THEME_ATTRIBUTE) || 'light';
          const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
          setTheme(newTheme);
        }
        
        function updateIcon(theme) {
          const icon = document.getElementById('darkModeIcon');
          if (icon) {
            if (theme === 'dark') {
              icon.classList.remove('bi-moon');
              icon.classList.add('bi-sun');
            } else {
              icon.classList.remove('bi-sun');
              icon.classList.add('bi-moon');
            }
          }
        }
        
        // Update icon on load
        updateIcon(theme);
        
        // Add click handler
        const toggleBtn = document.getElementById('darkModeToggle');
        if (toggleBtn) {
          toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleTheme();
          });
        }
      }
      
      // Initialize when DOM is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDarkMode);
      } else {
        initDarkMode();
      }
    })();
  </script>
  
  <!-- Navigation Active Indicator Script -->
  <script>
    (function() {
      "use strict";
      
      function initNavigation() {
        // Get current page from URL
        const currentPath = window.location.pathname;
        const currentPage = currentPath.split('/').pop() || 'index.php';
        const currentPageBase = currentPage.split('?')[0];
        
        // Remove all active classes first
        const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
        navLinks.forEach(link => {
          link.classList.remove('active');
        });

        // Comprehensive page mapping
        const pageMap = {
          'add_categories.php': 'categories.php',
          'manage_categories.php': 'categories.php',
          'add_series.php': 'series.php',
          'manage_series.php': 'series.php',
          'add_chapters.php': 'chapters.php',
          'manage_chapters.php': 'chapters.php',
          'add_blogs.php': 'blogs.php',
          'manage_blogs.php': 'blogs.php',
          'add_blog_feeds.php': 'blog_feeds.php',
          'manage_blog_feeds.php': 'blog_feeds.php',
          'add_owl_carousels.php': 'owl_carousels.php',
          'manage_owl_carousels.php': 'owl_carousels.php',
          'manage_users.php': 'users.php',
          'manage_admin.php': 'admin.php',
          'approve_payment.php': 'approve_payment.php',
          'pending_payment.php': 'pending_payment.php',
          'recent_payment.php': 'approve_payment.php',
          'unapprove_payment.php': 'approve_payment.php',
          'approved_payment.php': 'approve_payment.php',
          'users_profile.php': 'users_profile.php',
          'api_documentation.php': 'api_documentation.php',
          'pages_contact.php': 'pages_contact.php',
        };

        // Determine target page
        let targetPage = currentPageBase;
        if (pageMap[currentPageBase]) {
          targetPage = pageMap[currentPageBase];
        }
        
        // Special case for index
        if (currentPageBase === '' || currentPageBase === 'index.php' || 
            currentPath.endsWith('/admin/') || currentPath.endsWith('/admin')) {
          targetPage = 'index.php';
        }

        // Activate matching links
        navLinks.forEach(link => {
          const href = link.getAttribute('href');
          if (href) {
            const linkPage = href.split('/').pop().split('?')[0];
            if (linkPage === targetPage || linkPage === currentPageBase) {
              link.classList.add('active');
            }
          }
        });
      }

      // Initialize
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavigation);
      } else {
        initNavigation();
      }
      
      window.addEventListener('popstate', initNavigation);
    })();
  </script>