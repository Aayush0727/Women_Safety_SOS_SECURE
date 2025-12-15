const submitBtn = document.querySelector('.submit-btn'),
    phone = document.querySelector('#phone'),
    password = document.querySelector('#user-password'),
    passwordConfirm = document.querySelector('#user-password-confirm'),
    email = document.querySelector('#mail'),
    firstName = document.querySelector('#f-name'),
    lastName = document.querySelector('#l-name'),
    errorDisplayers = document.getElementsByClassName('error'),
    inputFields = document.querySelectorAll('input'),
    cardContainer = document.querySelector('.card-container'),
    outroOverlay = document.querySelector('.outro-overlay');

let count = 0;

function onValidation(current, messageString, booleanTest) {
    current.textContent = messageString;
    if (booleanTest) count++;
}

// Validation on input fields
for (let i = 0; i < inputFields.length; i++) {
    let currentInputField = inputFields[i];
    let currentErrorDisplayer = errorDisplayers[i];

    currentInputField.addEventListener('keyup', (e) => {
        e.target.value !== "" ? onValidation(currentErrorDisplayer, '', 1) : onValidation(currentErrorDisplayer, '*This field is required', 0);
    });
}

// Phone number validation (Only 10 digits)
phone.addEventListener('keyup', (e) => {
    let message = errorDisplayers[3];
    /^\d{10}$/.test(e.target.value) ? onValidation(message, '', 1) : onValidation(message, '*Please enter a valid 10-digit number', 0);
});

// Email validation
email.addEventListener('keyup', (e) => {
    let message = errorDisplayers[2];
    /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(e.target.value) ? onValidation(message, '', 1) : onValidation(message, '*Please provide a valid email', 0);
});

// Password validation (Min 8 characters, at least 1 uppercase & 1 special character)
password.addEventListener('keyup', (e) => {
    let message = errorDisplayers[4];
    let passwordRegex = /^(?=.*[A-Z])(?=.*[\W_]).{8,}$/;
    
    passwordRegex.test(password.value)
        ? onValidation(message, '', 1)
        : onValidation(message, '*Password must be at least 8 characters, include 1 uppercase & 1 special character', 0);
});

// Confirm password validation (Must match password)
passwordConfirm.addEventListener('keyup', (e) => {
    let message = errorDisplayers[5];
    password.value === e.target.value ? onValidation(message, '', 1) : onValidation(message, '*Passwords do not match', 0);
});

// Form submission via AJAX with validation
submitBtn.addEventListener('click', (e) => {
    e.preventDefault();
    
    let isValid = true;

    // Check if all fields are filled
    inputFields.forEach((field, index) => {
        if (field.value.trim() === "") {
            errorDisplayers[index].textContent = "*This field is required";
            isValid = false;
        }
    });

    // Phone validation
    if (!/^\d{10}$/.test(phone.value)) {
        errorDisplayers[3].textContent = "*Please enter a valid 10-digit number";
        isValid = false;
    }

    // Email validation
    if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email.value)) {
        errorDisplayers[2].textContent = "*Please provide a valid email";
        isValid = false;
    }

    // Password validation
    let passwordRegex = /^(?=.*[A-Z])(?=.*[\W_]).{8,}$/;
    if (!passwordRegex.test(password.value)) {
        errorDisplayers[4].textContent = "*Password must be at least 8 characters, include 1 uppercase & 1 special character";
        isValid = false;
    }

    // Confirm password validation
    if (password.value !== passwordConfirm.value) {
        errorDisplayers[5].textContent = "*Passwords do not match";
        isValid = false;
    }

    if (isValid) {
        // If all validations pass, proceed with form submission via AJAX
        let formData = new FormData(registration-form);
        formData.append('f_name', firstName.value);
        formData.append('l_name', lastName.value);
        formData.append('mail', email.value);
        formData.append('phone', phone.value);
        formData.append('user_password', password.value);
        formData.append('user_password_confirm', passwordConfirm.value);

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                window.location.href = "login.html" ; // Redirect to login page after successful registration
            } else {
                alert(data.message); // Show error message if registration fails
            }
        })
        .catch(error);
    }
});
