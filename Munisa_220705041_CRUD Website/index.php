<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "crud_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Variabel untuk pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 5;  // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total halaman
$sqlCount = "SELECT COUNT(*) as total FROM users WHERE name LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$searchLike = "%$search%";
$stmtCount->bind_param("s", $searchLike);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalData = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk menampilkan data dengan filter pencarian dan batas pagination
$sql = "SELECT * FROM users WHERE name LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchLike, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Tambah data pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sqlInsert = "INSERT INTO users (name, email, phone) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sss", $name, $email, $phone);
    if ($stmtInsert->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmtInsert->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD dengan Modal Tambah Pengguna</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
 body {
    background-color:rgb(192, 226, 252); 
    color: #333333; 
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.container {
    width: 100%;
    max-width: 900px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

h2 {
    text-align: center;
    color:rgb(33, 172, 211);
    font-weight: bold;
    margin-bottom: 20px;
}

.form-inline {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.form-inline input {
    width: 250px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-inline button {
    background-color: #2196f3; 
    color: #ffffff;
    border: none;
    border-radius: 20px;
    padding: 8px 15px;
    cursor: pointer;
}

.form-inline button:hover {
    background-color: #1e88e5; 
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.table th {
    background-color: #d9e7ff; 
    color:rgb(204, 241, 249);
    font-weight: bold;
    text-align: left;
    padding: 12px;
}

.table td {
    padding: 10px;
    border-bottom: 1px solid #e3e9ef;
    text-align: left;
}

.table tbody tr {
    background-color: #ffffff; 
}

.table tbody tr:hover {
    background-color:rgb(184, 212, 255); 
}

.btn {
    border-radius: 20px;
    font-size: 14px;
    padding: 5px 15px;
    text-align: center;
    display: inline-block;
    cursor: pointer;
}

.btn-edit {
    background-color: #64b5f6; 
    color: #ffffff;
    border: none;
}

.btn-edit:hover {
    background-color: #42a5f5; 
}

.btn-delete {
    background-color: #1e88e5; 
    color: #ffffff;
    border: none;
}

.btn-delete:hover {
    background-color: #1565c0;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination .page-item {
    margin: 0 5px;
}

.pagination .page-item a {
    display: block;
    padding: 8px 12px;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 5px;
    color: #007acc;
    text-decoration: none;
}

.pagination .page-item.active a {
    background-color: #42a5f5;
    color: #ffffff;
    border: none;
}

.add-user-btn {
    text-align: left;
    margin-bottom: 15px;
}

.modal-header {
    background-color: #42a5f5; 
    color: white;
}

.modal-footer .btn-secondary {
    background-color:rgb(215, 242, 255); 
    color: #333333;
}

input:focus {
    border-color: #42a5f5 !important;
    box-shadow: 0 0 5px rgba(66, 165, 245, 0.5);
}

    </style>
</head>
<body>
<div class="container">
    <h2>Daftar Pengguna</h2>

    <!-- Form Pencarian -->
    <form method="GET" action="" class="form-inline">
        <input type="text" name="search" value="<?php echo $search; ?>" class="form-control mr-2" placeholder="Cari nama..." style="width: 250px;">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <!-- Tombol Tambah Pengguna -->
    <div class="add-user-btn">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
            Tambah Pengguna
        </button>
    </div>

    <!-- Tabel Data Pengguna -->
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td>
                    <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="name">Nama:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Telepon:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
