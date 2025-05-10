<div class="navbar">
        <!-- logo -->
      <x-logo></x-logo>
      <div class="profile">
        <span>{{ session('user.username') ?? session('user.email') }}</span>
        <a href="{{ route('logout') }}" class="logout">Logout</a>
      </div>
    </div>
