function togglePasswordVisibility(inputId) {
    var passwordInput = document.getElementById(inputId);
    var eyeIcon = document.getElementById('eye-icon');
  
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      eyeIcon.src = '../ASSETS/open.png';
    } else {
      passwordInput.type = 'password';
      eyeIcon.src = '../ASSETS/close.png';
    }
  }