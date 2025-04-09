document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("loginForm");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  // Client-side validation
  function validateForm() {
    let isValid = true;
    
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();
    
    // Check if fields are empty
    if (username === "") {
      alert("Invalid username");
      isValid = false;
      return isValid;
    }
    
    // Email format validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(username)) {
      alert("Invalid username");
      isValid = false;
      return isValid;
    }
    
    if (password === "") {
      alert("Invalid password");
      isValid = false;
      return isValid;
    } else if (password.length < 6) {
      alert("Invalid password");
      isValid = false;
      return isValid;
    }
    
    return isValid;
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    
    if (validateForm()) {
      // Create FormData object
      const formData = new FormData(form);
      
      // Send AJAX request
      fetch("login.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Successful login
          // Store login info in localStorage
          localStorage.setItem('loggedIn', 'true');
          localStorage.setItem('username', data.username);
          
          // Redirect based on the intended destination
          const redirectUrl = sessionStorage.getItem('intendedDestination') || data.redirect || 'index.html';
          sessionStorage.removeItem('intendedDestination'); // Clear the stored destination
          window.location.href = redirectUrl;
        } else {
          // Handle errors with alerts
          alert(data.message);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("An error occurred. Please try again later.");
      });
    }
  });
});