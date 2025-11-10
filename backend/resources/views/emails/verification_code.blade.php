<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verification Code</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="width: 90%; margin: auto; padding: 20px;">
        <h2>Hello!</h2>
        <p>Your 2-factor verification code is:</p>
        <h1 style="font-size: 3em; letter-spacing: 5px; margin: 20px 0;">
            {{ $code }}
        </h1>
        <p>This code will expire in 10 minutes.</p>
    </div>
</body>

</html>