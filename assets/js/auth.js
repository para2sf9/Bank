// Auth Pages - Password Toggle, Strength Meter, Form Handling

document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    window.togglePassword = function(btn) {
        const input = btn.parentElement.querySelector('input');
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Password strength meter
    const passwordInput = document.getElementById('passwordInput');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    const passwordHint = document.getElementById('passwordHint');

    if (passwordInput && passwordStrength && strengthBar) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;

            if (password.length === 0) {
                passwordStrength.classList.remove('show');
                if (passwordHint) passwordHint.style.display = 'block';
                return;
            }

            passwordStrength.classList.add('show');
            if (passwordHint) passwordHint.style.display = 'none';

            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';

            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
    }

    // Password match checker
    const confirmInput = document.getElementById('confirmPasswordInput');
    const passwordMatch = document.getElementById('passwordMatch');

    if (confirmInput && passwordMatch && passwordInput) {
        function checkMatch() {
            const pass = passwordInput.value;
            const confirm = confirmInput.value;

            if (confirm.length === 0) {
                passwordMatch.textContent = '';
                passwordMatch.className = 'password-match';
                return;
            }

            if (pass === confirm) {
                passwordMatch.textContent = 'Passwords match';
                passwordMatch.className = 'password-match match';
            } else {
                passwordMatch.textContent = 'Passwords do not match';
                passwordMatch.className = 'password-match mismatch';
            }
        }

        confirmInput.addEventListener('input', checkMatch);
        passwordInput.addEventListener('input', checkMatch);
    }

    // Form submission loading state
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const originalText = btnText ? btnText.textContent : submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';

                if (btnText) {
                    btnText.textContent = 'Processing...';
                }

                const icon = submitBtn.querySelector('.btn-icon');
                if (icon) {
                    icon.className = 'fas fa-circle-notch fa-spin btn-icon';
                }
            }
        });
    });

    // Input focus effects
    const inputs = document.querySelectorAll('.form-group input, .form-group select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            const wrapper = this.closest('.input-wrapper');
            if (wrapper) {
                wrapper.style.transform = 'translateY(-1px)';
            }
        });

        input.addEventListener('blur', function() {
            const wrapper = this.closest('.input-wrapper');
            if (wrapper) {
                wrapper.style.transform = 'translateY(0)';
            }
        });
    });
});
