// Wait for the HTML document to be fully loaded and parsed.
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.matrimonial-form');
    const inputs = form.querySelectorAll('input, textarea, select');

    // SVG icons for tick and cross
    const tickSVG = `<span class="input-icon valid-icon">&#10003;</span>`;
    const crossSVG = `<span class="input-icon invalid-icon">&#10007;</span>`;

    // Prevent numbers in Full Name, Occupation, Father's Name, Mother's Name
    ['fullName', 'occupation', 'fatherName', 'motherName'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function () {
                this.value = this.value.replace(/[0-9]/g, '');
            });
        }
    });

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

    // Validation rules
    function validate(input) {
        if (input.disabled || input.type === 'fieldset' || input.type === 'button') return true;
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
        // Check radio groups
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
});
