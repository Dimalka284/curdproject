<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat with Users</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .user-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
        .user-name { font-weight: bold; }
        .message-btn { background-color: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .message-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Available Users</h1>
    
    @if ($users->count() > 0)
        @foreach ($users as $user)
            <div class="user-card">
                <span class="user-name">{{ $user->name }}</span>
                {{-- Link to Chatify route with the user ID --}}
                <a href="{{ route('chatify', $user->id) }}" class="message-btn">Message</a>
            </div>
        @endforeach
    @else
        <p>No other users found.</p>
    @endif

</body>
</html>
