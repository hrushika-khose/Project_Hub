// register.js

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const password = form.querySelector("input[name='password']");
    const confirmPassword = form.querySelector("input[name='confirm_password']");
  
    form.addEventListener("submit", function (e) {
      // Password match check
      if (password.value !== confirmPassword.value) {
        e.preventDefault();
        alert("❌ Passwords do not match!");
        return;
      }
  
      // Email format check
      const email = form.querySelector("input[name='email']");
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  
      if (!emailPattern.test(email.value)) {
        e.preventDefault();
        alert("⚠️ Please enter a valid email address.");
        return;
      }
  
      // Password length check
      if (password.value.length < 6) {
        e.preventDefault();
        alert("🔐 Password should be at least 6 characters long.");
      }
    });
  });
  