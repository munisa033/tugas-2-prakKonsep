<?php
$conn = new mysqli("localhost", "root", "", "crud_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Pengguna</title>
    <style>
        /* Mengatur gaya umum untuk body dengan warna biru muda */
        body {
            font-family: Arial, sans-serif;
            background-color: #add8e6; /* Light blue background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Mengatur gaya container form */
        form {
            background-color: #ffffff; /* Warna putih untuk form */
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 350px;
            border: 1px solid #4682b4; /* Steel blue border */
        }

        /* Mengatur label input dan spasi antar elemen */
        form input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #b3d4e5; /* Light blue border */
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #f0f8ff; /* Alice blue background */
        }

        /* Mengatur gaya tombol submit */
        form button {
            width: 100%;
            padding: 12px;
            background-color: #4682b4; /* Steel blue button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Mengatur gaya tombol submit saat di-hover */
        form button:hover {
            background-color: #5f9ea0; /* Darker blue on hover */
        }

        /* Mengatur gaya teks label */
        form label {
            font-weight: bold;
            color: #4682b4; /* Steel blue text */
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="name">Nama:</label>
        <input type="text" name="name" value="<?php echo $name; ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $email; ?>" required>
        
        <label for="phone">Telepon:</label>
        <input type="text" name="phone" value="<?php echo $phone; ?>" required>
        
        <button type="submit">Update</button>
    </form>
</body>
</html>
