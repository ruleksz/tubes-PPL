<!DOCTYPE html>
<html>
<head>
    <title>NASA APOD</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>{{ $data['title'] }}</h1>
    <p>{{ $data['date'] }}</p>
    <p>{{ $data['explanation'] }}</p>

    @if(isset($data['url']))
        <img src="{{ $data['url'] }}" alt="NASA Image of the Day">
    @endif

</body>
</html>