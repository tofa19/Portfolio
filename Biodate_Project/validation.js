// Wait for the HTML document to be fully loaded and parsed.
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.matrimonial-form');
    const inputs = form.querySelectorAll('input, textarea, select');

    // Check if this is a login page or registration page
    const isLoginPage = document.querySelector('input[name="email"]') && document.querySelector('input[name="password"]') && inputs.length <= 3;

    // SVG icons for tick and cross
    const tickSVG = `<span class="input-icon valid-icon">&#10003;</span>`;
    const crossSVG = `<span class="input-icon invalid-icon">&#10007;</span>`;

    // Prevent numbers in Full Name, Occupation, Father's Name, Mother's Name (only for registration)
    if (!isLoginPage) {
        ['fullName', 'occupation', 'fatherName', 'motherName'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function () {
                    this.value = this.value.replace(/[0-9]/g, '');
                });
            }
        });
    }

    // Remove icons
    function removeIcon(input) {
        const parent = input.parentElement;
        const icon = parent.querySelector('.input-icon');
        if (icon) icon.remove();
    }

    // Add icon
    function addIcon(input, isValid) {
        removeIcon(input);
        const parent = input.parentElement;
        parent.style.position = 'relative';
        input.insertAdjacentHTML('afterend', isValid ? tickSVG : crossSVG);
    }

    // Enhanced password validation function
    function validatePassword(password) {
        if (isLoginPage) {
            // For login: only check minimum length
            return password.length >= 6;
        } else {
            // For registration: check all requirements
            const hasLength = password.length >= 6;
            const hasUpper = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            
            return hasLength && hasUpper && hasNumber && hasSpecial;
        }
    }

    // Validation rules
    function validate(input) {
        if (input.disabled || input.type === 'fieldset' || input.type === 'button') return true;
        
        // Password validation
        if (input.type === 'password') {
            return validatePassword(input.value);
        }
        
        // Login page validation
        if (isLoginPage) {
            if (input.type === 'email') {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim());
            }
            return input.value.trim() !== '';
        }
        
        // Registration page validation
        if (input.type === 'radio') {
            const group = form.querySelectorAll(`input[name="${input.name}"]`);
            return Array.from(group).some(r => r.checked);
        }
        if (input.type === 'checkbox') {
            return true; // Not required
        }
        if (input.type === 'email') {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim());
        }
        if (input.type === 'tel') {
            return /^\d{10,15}$/.test(input.value.replace(/\D/g, ''));
        }
        if (input.type === 'number') {
            if (input.value.trim() === '') return false;
            if (input.name === 'age') {
                return input.value >= 18 && input.value <= 80;
            }
            if (input.name === 'siblings') {
                return input.value >= 0 && input.value <= 10;
            }
            return true;
        }
        if (input.type === 'file') {
            return input.required ? input.files.length > 0 : true;
        }
        if (input.tagName.toLowerCase() === 'select') {
            return input.value !== '';
        }
        // Prevent numbers for specific fields
        if (
            input.name === 'fullName' ||
            input.name === 'occupation' ||
            input.name === 'fatherName' ||
            input.name === 'motherName'
        ) {
            return /^[A-Za-z\s]+$/.test(input.value.trim()) && input.value.trim() !== '';
        }
        // Default for text, textarea
        return input.value.trim() !== '';
    }

    // Real-time validation
    inputs.forEach(input => {
        if (input.type === 'radio' || input.type === 'checkbox') {
            input.addEventListener('change', () => {
                const group = form.querySelectorAll(`input[name="${input.name}"]`);
                group.forEach(radio => {
                    if (validate(radio)) {
                        radio.parentElement.style.border = '2px solid #4CAF50';
                    } else {
                        radio.parentElement.style.border = '2px solid #F44336';
                    }
                });
            });
        } else {
            input.addEventListener('input', () => {
                if (input.value.trim() === '') {
                    input.style.border = '';
                    removeIcon(input);
                } else if (validate(input)) {
                    input.style.border = '2px solid #4CAF50';
                    addIcon(input, true);
                } else {
                    input.style.border = '2px solid #F44336';
                    addIcon(input, false);
                }
            });
        }
    });

    // On submit
    form.addEventListener('submit', function (e) {
        let allValid = true;
        
        // For login page
        if (isLoginPage) {
            inputs.forEach(input => {
                if (input.value.trim() === '' || !validate(input)) {
                    allValid = false;
                    input.style.border = '2px solid #F44336';
                    addIcon(input, false);
                }
            });
            if (!allValid) {
                e.preventDefault();
                alert('Please fill in all fields correctly.');
            }
            return;
        }
        
        // For registration page
        inputs.forEach(input => {
            if (
                (input.type !== 'checkbox' && input.type !== 'radio' && input.hasAttribute('required') && input.value.trim() === '') ||
                !validate(input)
            ) {
                allValid = false;
                input.style.border = '2px solid #F44336';
                addIcon(input, false);
            }
        });
        
        // Check radio groups for registration
        const radioGroups = new Set();
        form.querySelectorAll('input[type="radio"]').forEach(radio => {
            if (!radioGroups.has(radio.name)) {
                radioGroups.add(radio.name);
                const group = form.querySelectorAll(`input[name="${radio.name}"]`);
                if (!Array.from(group).some(r => r.checked)) {
                    allValid = false;
                    group.forEach(r => r.parentElement.style.border = '2px solid #F44336');
                }
            }
        });
        
        if (!allValid) {
            e.preventDefault();
        }
    });

    // Real-time password validation (only for registration page)
    const passwordField = document.getElementById('password');
    const requirements = document.querySelectorAll('.password-requirements li');
    
    if (passwordField && requirements.length > 0 && !isLoginPage) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            
            // Check length
            if (requirements[0]) {
                if (password.length >= 6) {
                    requirements[0].classList.add('valid');
                    requirements[0].classList.remove('invalid');
                } else {
                    requirements[0].classList.add('invalid');
                    requirements[0].classList.remove('valid');
                }
            }
            
            // Check uppercase
            if (requirements[1]) {
                if (/[A-Z]/.test(password)) {
                    requirements[1].classList.add('valid');
                    requirements[1].classList.remove('invalid');
                } else {
                    requirements[1].classList.add('invalid');
                    requirements[1].classList.remove('valid');
                }
            }
            
            // Check number
            if (requirements[2]) {
                if (/[0-9]/.test(password)) {
                    requirements[2].classList.add('valid');
                    requirements[2].classList.remove('invalid');
                } else {
                    requirements[2].classList.add('invalid');
                    requirements[2].classList.remove('valid');
                }
            }
            
            // Check special character
            if (requirements[3]) {
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    requirements[3].classList.add('valid');
                    requirements[3].classList.remove('invalid');
                } else {
                    requirements[3].classList.add('invalid');
                    requirements[3].classList.remove('valid');
                }
            }
        });
    }
});

// Toggle password visibility (for login page)
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggle = document.querySelector('.password-toggle');
    
    if (passwordField && toggle) {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggle.textContent = '🙈';
        } else {
            passwordField.type = 'password';
            toggle.textContent = '👁️';
        }
    }
}