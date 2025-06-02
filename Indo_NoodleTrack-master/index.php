<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indo Noodle Track</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color:hsl(189, 64.70%, 43.30%); /* Warna latar belakang yang lebih gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Mengisi tinggi layar */
        }

        .container {
            text-align: center;
            color: white; /* Warna teks */
        }

        h1 {
            font-size: 64px; /* Ukuran font untuk judul yang lebih besar */
            margin: 0;
            letter-spacing: 2px; /* Jarak antar huruf */
        }

        .button-container {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .button {
            flex: 1;
            padding: 12px 25px;
            background-color: #FFFFFF; /* Warna latar belakang tombol */
            color:rgb(9, 176, 205); /* Warna teks tombol */
            text-decoration: none;
            border-radius: 30px; /* Sudut tombol yang lebih bulat */
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s; /* Transisi halus */
            text-align: center;
        }

        .button:hover {
            background-color:rgb(21, 191, 197); /* Warna latar belakang saat hover */
            color: white; /* Warna teks saat hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1></h1>

        <img src="assets/images/logoindonoodletrack2.png" alt="Logo">
        <div class="button-container">
            <a href="./views/auth/login.php" class="button">Login</a>
            <a href="./views/auth/signup.php" class="button">Sign Up</a>
        </div>
    </div>
</body>
</html>