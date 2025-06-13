<x-layout>
    <div class="container">
        <h1 class="text-3xl font-bold mb-6">Users Management</h1>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <table id="mytable" border="1">
            <thead>
                <th>index</th>
                <th>username</th>
                <th>image</th>
                <th>email</th>
                <th>role</th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user['username'] ?? 'N/A' }}</td>
                        <td><img src="{{$user['image_url']}}" width="25px" loading="lazy" alt="user image"></td>
                        <td>{{ $user['email'] ?? 'N/A' }}</td>
                        <td>{{ $user['role'] }}</td>
                        <td>
                            <span class="badge bg-{{ $user['status'] === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($user['status']) }}
                            </span>
                        </td>
                        <td>
                            @if($user['role'] !== 'admin' && $user['id'] !== session('user_id'))
                                <form action="{{ route('users.toggle-block', $user['id']) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="action-btn {{ $user['status'] === 'active' ? 'block-btn bg-[#dc2626] hover:bg-[#b91c1c]' : 'unblock-btn bg-[#059669] hover:bg-[#047857]' }} text-white px-4 py-2 rounded-xl transition-colors cursor-pointer">
                                        {{ $user['status'] === 'active' ? 'Block' : 'Unblock' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">Not available</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layout>
