import {getAuth, signInWithEmailAndPassword} from 'firebase/auth';
import {app} from './firebase';

const auth = getAuth(app);
document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const userCredential = await signInWithEmailAndPassword(auth, email, password);
        document.getElementById('idToken').value = await userCredential.user.getIdToken();
        e.target.submit();
    } catch (error) {
        document.getElementById('error-message').textContent = "Login failed: " + error.message;
    }
});
