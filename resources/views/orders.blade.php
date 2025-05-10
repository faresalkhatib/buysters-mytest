<x-layout>
    <h1>welcome to orders</h1>
    <table id="mytable" border="1">
        <thead>
        <th>index</th>
        <th>username</th>
        <th>image</th>
        <th>email</th>
        <th>role</th>
        </thead>
        <tbody>
        @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user['username'] ?? 'N/A' }}</td>
                <td><img src="{{$user['image_url']}}" width="25px"></td>
                <td>{{ $user['email'] ?? 'N/A' }}</td>
                <td>{{ $user['role'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</x-layout>
