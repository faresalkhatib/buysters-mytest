<x-auth.layout>
    <style>
      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #eef1f5;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
      }

    </style>

    <div class="auth-wrapper">
      <div class="auth-left">
        <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Buysters Logo" style="width: 60px; margin-bottom: 20px;">
        <h1>Buysters Admin</h1>
        <p>Secure access to your store dashboard to manage orders, products, and customers.</p>
      </div>

      <div class="auth-right">
        <h2>Admin Panel Login</h2>
        <form action="{{ route('login.store') }}" method="POST" id="login-form">
          @csrf
          <input type="hidden" name="idToken" id="idToken">

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
          </div>

          <div class="forgot-password">
            <a href="#">Forgot password?</a>
          </div>

          @if ($errors->has('auth'))
            <div class="alert">{{ $errors->first('auth') }}</div>
          @endif



          <button type="submit" class="btn">Sign In</button>
        </form>
      </div>
    </div>
  </x-auth.layout>
