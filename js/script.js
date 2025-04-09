document.addEventListener("DOMContentLoaded", function() {
    // Check if user is logged in
    const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
    const username = localStorage.getItem('username');
    
    // Update login/profile button on EVERY page
    const loginBtn = document.getElementById('login-btn');
    if (loginBtn) {
      if (isLoggedIn && username) {
        loginBtn.innerHTML = `<a href="profile.html">${username}</a>`;
      } else {
        loginBtn.innerHTML = `<a href="login.html">Login</a>`;
      }
    }
    
    // Add auth check for protected links
    const protectedLinks = document.querySelectorAll('a[href="projects.html"], a[href="upload.html"]');
    protectedLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        if (!isLoggedIn) {
          e.preventDefault();
          // Store intended destination
          sessionStorage.setItem('intendedDestination', this.getAttribute('href'));
          // Redirect to login
          window.location.href = 'login.html';
        }
      });
    });
    
    // Add logout functionality if there's a logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // Clear local storage
        localStorage.removeItem('loggedIn');
        localStorage.removeItem('username');
        
        // Clear PHP session
        fetch('logout.php')
          .then(() => {
            window.location.href = 'index.html';
          })
          .catch(err => {
            console.error('Logout error:', err);
            window.location.href = 'index.html';
          });
      });
    }
    
    // Check if current page is protected
    const currentPath = window.location.pathname;
    const protectedPaths = ['/projects.html', '/upload.html'];
    
    const protectedPathCheck = protectedPaths.some(path => 
      currentPath.endsWith(path) || 
      currentPath.includes(path.replace('.html', ''))
    );
    
    if (protectedPathCheck && !isLoggedIn) {
      // Store current location
      sessionStorage.setItem('intendedDestination', currentPath);
      // Redirect to login
      window.location.href = 'login.html';
    }
    setTimeout(() => {
      const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
      const username = localStorage.getItem('username');
      const loginBtn = document.getElementById('login-btn');
      
      if (loginBtn && isLoggedIn && username) {
        console.log("Retry update of login button");
        loginBtn.innerHTML = `<a href="profile.html">${username}</a>`;
      }
    }, 100);
  });