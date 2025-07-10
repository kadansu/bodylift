document.addEventListener('DOMContentLoaded', () => {
    // Input elements
    const firstName = document.getElementById('first_name');
    const lastName = document.getElementById('last_name');
    const emailInput = document.getElementById('email');
    const ageInput = document.getElementById('age');
    const passwordInput = document.getElementById('password');
    const form = document.querySelector('form');

    // Error display elements
    const firstNameError = document.getElementById('first-name-error');
    const lastNameError = document.getElementById('last-name-error');
    const emailError = document.getElementById('email-error');
    const ageError = document.getElementById('age-error');

    const validNamePattern = /^[a-zA-Z\s'-]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // First Name Validation
    firstName.addEventListener('input', () => {
        const value = firstName.value.trim();
        firstNameError.textContent = validNamePattern.test(value)
            ? ''
            : 'Only letters, spaces, apostrophes, and hyphens are allowed.';
    });

    //  Last Name Validation
    lastName.addEventListener('input', () => {
        const value = lastName.value.trim();
        lastNameError.textContent = validNamePattern.test(value)
            ? ''
            : 'Only letters, spaces, apostrophes, and hyphens are allowed.';
    });

    // Email Validation
    emailInput.addEventListener('input', () => {
        const email = emailInput.value.trim();
        emailError.textContent = emailPattern.test(email)
            ? ''
            : 'Please enter a valid email address.';
    });

    // Age Validation
    ageInput.addEventListener('input', () => {
        const age = parseInt(ageInput.value);
        ageError.textContent = (isNaN(age) || age < 15)
            ? 'You must be at least 15 years old.'
            : '';
    });

    // On Form Submit
    form.addEventListener('submit', (e) => {
        let valid = true;

        // Reset errors
        firstNameError.textContent = '';
        lastNameError.textContent = '';
        emailError.textContent = '';
        ageError.textContent = '';

        // Re-validate all fields
        const fName = firstName.value.trim();
        const lName = lastName.value.trim();
        const email = emailInput.value.trim();
        const age = parseInt(ageInput.value);
        const password = passwordInput.value;

        if (!validNamePattern.test(fName)) {
            firstNameError.textContent = 'Only letters, spaces, apostrophes, and hyphens are allowed.';
            valid = false;
        }

        if (!validNamePattern.test(lName)) {
            lastNameError.textContent = 'Only letters, spaces, apostrophes, and hyphens are allowed.';
            valid = false;
        }

        if (!emailPattern.test(email)) {
            emailError.textContent = 'Please enter a valid email address.';
            valid = false;
        }

        if (isNaN(age) || age < 15) {
            ageError.textContent = 'You must be at least 15 years old.';
            valid = false;
        }

        if (password.length < 6) {
            alert('Password must be at least 6 characters long.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
