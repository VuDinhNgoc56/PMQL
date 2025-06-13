<?php
session_start();

// === KẾT NỐI SQL SERVER ===
$serverName = "VUDINHNGOC\\SQLSERVER2023"; // Lưu ý: dấu \\ để escape ký tự \
$connectionOptions = [
    "Database" => "Duannhom3",      // <<== THAY bằng tên database thật của bạn
    "Uid" => "sa123",
    "PWD" => "123",               // <<== THAY bằng mật khẩu SQL thật
    "Encrypt" => true,
    "TrustServerCertificate" => true
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die("Kết nối thất bại: " . print_r(sqlsrv_errors(), true));
}

// === XỬ LÝ ĐĂNG NHẬP ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $adminCode = $_POST['admin_code'] ?? '';

    $sql = "SELECT * FROM users WHERE (name = ? OR email = ?) AND role = 'admin'";
    $params = [$usernameOrEmail, $usernameOrEmail];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Kiểm tra mật khẩu đã mã hóa
        if (password_verify($password, $user['password_hash'])) {

            // Kiểm tra mã admin
            if ($user['admin_code'] !== $adminCode) {
                echo "❌ Mã admin không đúng.";
                exit;
            }

            // Đăng nhập thành công
            $_SESSION['name'] = $user['name'];
            $_SESSION['password_hash'] = $user['password_hash'];
            $_SESSION['admin_code'] = $user['admin_code'];
            echo "<script>
             alert('✅ Đăng nhập thành công với tư cách admin!');
             window.location.href = 'TC.html';
            </script>";
exit;

        } else {
            echo "❌ Sai mật khẩu.";
        }
    } else {
        echo "❌ Không tìm thấy tài khoản admin phù hợp.";
    }
}
// === XỬ LÝ ĐĂNG KÝ ADMIN ===
if (isset($_POST['register'])) {
    $name = $_POST['reg_name'] ?? '';
    $email = $_POST['reg_email'] ?? '';
    $password = $_POST['reg_password'] ?? '';
    $created_at = date("Y-m-d H:i:s");

    // Mã hóa mật khẩu
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = "admin";

    // === TỰ ĐỘNG TẠO MÃ ADMIN NGẪU NHIÊN ===
    function generateAdminCode($length = 6) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return 'ADMIN-' . $code;
    }
    $adminCode = generateAdminCode();

    // Thêm vào CSDL
    $sql = "INSERT INTO users (name, email, password_hash, role, admin_code, created_at)
            VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$name, $email, $password_hash, $role, $adminCode, $created_at];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "✅ Đăng ký admin thành công! Mã admin của bạn là: <strong>$adminCode</strong>";
    } else {
        echo "❌ Lỗi khi đăng ký: ";
        print_r(sqlsrv_errors(), true);
    }
}
?>
