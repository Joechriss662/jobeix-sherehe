<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Message Status</title>
</head>
<body>
    <h1>WhatsApp Message Status</h1>
    @if ($status === 'success')
        <p style="color: green;">{{ $message }}</p>
    @else
        <p style="color: red;">{{ $message }}</p>
    @endif
</body>
</html>