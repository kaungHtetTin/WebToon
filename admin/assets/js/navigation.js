/**
 * Navigation Enhancement - Google Console Style
 * Auto-highlights active navigation items with improved page matching
 */

(function() {
  "use strict";

  function initNavigation() {
    // Get current page from URL
    const currentPath = window.location.pathname;
    const currentPage = currentPath.split('/').pop() || 'index.php';
    const currentPageBase = currentPage.split('?')[0]; // Remove query parameters
    
    // Remove all active classes first
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    navLinks.forEach(link => {
      link.classList.remove('active');
    });

    // Comprehensive page mapping for manage/add pages
    const pageMap = {
      // Categories
      'add_categories.php': 'categories.php',
      'manage_categories.php': 'categories.php',
      
      // Series
      'add_series.php': 'series.php',
      'manage_series.php': 'series.php',
      
      // Blogs
      'add_blogs.php': 'blogs.php',
      'manage_blogs.php': 'blogs.php',
      
      // Owl Carousels
      'add_owl_carousels.php': 'owl_carousels.php',
      'manage_owl_carousels.php': 'owl_carousels.php',
      
      // Users & Admin
      'manage_users.php': 'users.php',
      'manage_admin.php': 'admin.php',
      
      // Payments
      'payment_methods.php': 'payment_methods.php',
      'add_payment_methods.php': 'payment_methods.php',
      'manage_payment_methods.php': 'payment_methods.php',
      'approve_payment.php': 'approve_payment.php',
      'pending_payment.php': 'pending_payment.php',
      'recent_payment.php': 'approve_payment.php',
      'unapprove_payment.php': 'approve_payment.php',
      'approved_payment.php': 'approve_payment.php',
      
      // Profile
      'users_profile.php': 'users_profile.php',
      
      // Contact
      'pages_contact.php': 'pages_contact.php',
    };

    // Determine which page should be active
    let targetPage = currentPageBase;
    if (pageMap[currentPageBase]) {
      targetPage = pageMap[currentPageBase];
    }

    // Special case for index.php
    if (currentPageBase === '' || currentPageBase === 'index.php' || currentPath.endsWith('/admin/') || currentPath.endsWith('/admin')) {
      targetPage = 'index.php';
    }

    // Find and activate matching nav links
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href) {
        const linkPage = href.split('/').pop().split('?')[0];
        
        // Direct match
        if (linkPage === targetPage) {
          link.classList.add('active');
        }
        // Also check if current page matches directly
        else if (linkPage === currentPageBase) {
          link.classList.add('active');
        }
      }
    });

    // Debug log (remove in production)
    // console.log('Current page:', currentPageBase, 'Target page:', targetPage);
  }

  // Initialize on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavigation);
  } else {
    initNavigation();
  }

  // Re-run on navigation (for SPA-like behavior if needed)
  window.addEventListener('popstate', initNavigation);

})();

