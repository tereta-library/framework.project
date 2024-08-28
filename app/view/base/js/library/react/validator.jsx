export function validatorRequired(value, error, setError, config = {}) {
    if (!value) {
        setError('This field is required');
    }
    else if (config.formMode && formMode.current && error) {
        // Show already set message
    }
    else {
        setError('');
    }
}

export function validatorEmail(email, errorEmail, setErrorEmail, config = {}) {
    const errorList = {
        required: 'Email address is required',
        invalid: 'Invalid email address'
    }

    if (config.required && !email) {
        setErrorEmail(errorList.required);
    }
    else if (email && !email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)) {
        setErrorEmail(errorList.invalid);
    } else if (errorEmail && Object.values(errorList).includes(errorEmail)) {
        setErrorEmail('');
    }
}

export function validatorPhone(phone, errorPhone, setErrorPhone, config = {}) {
    if (config.required && !phone) {
        setErrorPhone('Phone number is required');
    }
    else if (config.formMode && config.formMode.current && errorPhone) {
        // Show already set message
    }
    else if (phone && !phone.match(/^\+?[\-()0-9 ]+$/)) {
        setErrorPhone('Invalid phone number');
    } else {
        setErrorPhone('');
    }
}

export function validatorPassword(password, passwordVerify, setErrorPassword, setErrorPasswordVerify, config = {}) {
    if (config.required && !password) {
        setErrorPassword('Password is required');
        setErrorPasswordVerify('');
    } else if (password && !password.match(/^.{6,}$/)) {
        setErrorPassword('Passwords should be at least 6 characters long');
        setErrorPasswordVerify('');
    } else if (password && password !== passwordVerify) {
        setErrorPasswordVerify('Passwords do not match');
        setErrorPassword('');
    } else {
        setErrorPasswordVerify('');
        setErrorPassword('');
    }
}
