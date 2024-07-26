function togglePasswordVisibility() {
    let passwordInput = document.getElementById('password');
    let toggleButton = document.querySelector('.toggle-password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.innerHTML = 'visibility';
    } else {
        passwordInput.type = 'password';
        toggleButton.innerHTML = 'visibility_off';
    }
}
