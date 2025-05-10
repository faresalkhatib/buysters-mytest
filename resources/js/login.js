import {getAuth, signInWithEmailAndPassword} from 'firebase/auth';
import {app} from './firebase';

const auth = getAuth(app);
document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const error_filed = document.getElementById('error-message');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!(email || password)) {
        error_filed.textContent = 'Please fill all the required fields.';
        return
    }

    if (!emailRegex.test(email)) {
        error_filed.textContent = 'Please enter a valid email';
    }

    try {
        const userCredential = await signInWithEmailAndPassword(auth, email, password);
        document.getElementById('idToken').value = await userCredential.user.getIdToken();
        e.target.submit();
    } catch (error) {
        error_filed.textContent = "Login failed: " + error.message;
    }
});
