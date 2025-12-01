/**
 * Navigation Enhancement - Google Console Style
 * Auto-highlights active navigation items
 */

(function() {
  "use strict";

  function initNavigation() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href) {
        const linkPage = href.split('/').pop();
        
        // Check if current page matches
        if (linkPage === currentPage || 
            (currentPage === '' && linkPage === 'index.php') ||
            (currentPage === 'index.php' && linkPage === 'index.php')) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      }
    });

    // Also check for query parameters (e.g., manage_categories.php?update=1 should highlight categories.php)
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
      'users_profile.php': 'users_profile.php',
      'approve_payment.php': 'approve_payment.php',
      'pending_payment.php': 'pending_payment.php',
      'recent_payment.php': 'recent_payment.php',
      'unapprove_payment.php': 'approve_payment.php',
      'approved_payment.php': 'approve_payment.php',
    };

    if (pageMap[currentPage]) {
      const parentPage = pageMap[currentPage];
      navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(parentPage)) {
          link.classList.add('active');
        }
      });
    }
  }

  // Initialize on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavigation);
  } else {
    initNavigation();
  }

})();

