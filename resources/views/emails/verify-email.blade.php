<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Barcode Store - Verify Your Email</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding: 50px;
            font-family: "Cairo", sans-serif;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #333;
        }

        .logo {
            width: 90%;
            height: 320px;
            margin: 10px auto;
        }

        .logo img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }

        .button {
            display: inline-block;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #0b75ee;
            color: #fff !important;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        p {
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="http://localhost:8000/assets/barcodelogo.png"
                alt="Barcode Store Logo">
        </div>
        <div class="content">
            <h1>Verify Your Email</h1>
            <p>Hi <b>{{ $userName }}</b> ,</p>
            <p>Thank you for signing up! Please verify your email address by clicking the link below:</p>
            <a href="{{ $verificationUrl }}" class="button">Verify Email</a>
            <p>If you did not create an account, please ignore this email.</p>
            <p>Best regards,<br>The Barcode Store Team</p>
        </div>
        <footer class="footer">
            <p>&copy; 2025 Barcode Store. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>
