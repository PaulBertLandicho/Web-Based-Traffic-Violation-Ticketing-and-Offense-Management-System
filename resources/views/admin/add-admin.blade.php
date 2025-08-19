@if(session('success'))
<div style="color: green">{{ session('success') }}</div>
@endif

@if(session('error'))
<div style="color: red">{{ session('error') }}</div>
@endif

@if ($errors->any())
<ul style="color:red;">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

<form method="POST" action="{{ route('admin.store') }}">
    @csrf
    <input type="text" name="admin_name" placeholder="Admin Name" value="{{ old('admin_name') }}" required>
    <input type="email" name="admin_email" placeholder="Email" value="{{ old('admin_email') }}" required>
    <input type="password" name="admin_password" placeholder="Password" required>
    <button type="submit">Create Admin</button>
</form>