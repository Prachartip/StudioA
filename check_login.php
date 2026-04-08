<?php
session_start();
include('config.php');

// ตัวแปรสำหรับเช็คว่ามี Error ไหม
$error_message = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // ค้นหาชื่อผู้ใช้ในฐานข้อมูล
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // ตรวจสอบรหัสผ่าน (ถ้ารหัสผ่านตรงกัน)
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $error_message = "รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง";
        }
    } else {
        $error_message = "ไม่พบชื่อผู้ใช้นี้ในระบบ";
    }
} else {
    // ถ้าไม่ได้กดปุ่มมาจากหน้า login แต่แอบพิมพ์ URL เข้ามาตรงๆ
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบไม่สำเร็จ - STUDIO A</title>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Kanit', sans-serif;
            background: #0a0a0f; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }
        
        .error-container { width: 100%; max-width: 400px; position: relative; z-index: 10; text-align: center; }
        .error-card {
            background: #151520; 
            border: 1px solid #2a2a35; border-radius: 16px;
            padding: 50px 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 71, 87, 0.1);
            position: relative; backdrop-filter: blur(20px);
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        
        /* เส้นขอบบนสีแดงแจ้งเตือน */
        .error-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, #ff4757, transparent); 
        }

        /* ไอคอนกากบาทสีแดง */
        .error-icon {
            width: 70px; height: 70px;
            background: rgba(255, 71, 87, 0.1);
            border: 2px solid #ff4757;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px auto;
            color: #ff4757; font-size: 30px; font-weight: bold;
            box-shadow: 0 0 20px rgba(255, 71, 87, 0.3);
        }

        .error-card h2 { 
            font-family: 'Chakra Petch', sans-serif;
            color: #ffffff; font-size: 1.8rem; font-weight: 700; margin-bottom: 10px; letter-spacing: 1px; 
        }
        
        .error-card p { 
            color: #ff6b81; font-size: 16px; margin-bottom: 30px; 
        }

        /* ปุ่มกดกลับไปหน้าล็อกอิน */
        .back-btn {
            display: inline-block;
            width: 100%; 
            background: linear-gradient(135deg, #ff4757, #ff6b81); 
            border: none; border-radius: 6px;
            padding: 16px 32px; 
            color: #ffffff; 
            font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none;
            transition: 0.3s; text-transform: uppercase; letter-spacing: 1px;
        }
        .back-btn:hover {
            transform: translateY(-2px); box-shadow: 0 10px 30px rgba(255, 71, 87, 0.4);
        }

        /* เอฟเฟกต์ลูกแก้วสีแดงด้านหลัง */
        .background-effects { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
        .glow-orb { position: absolute; border-radius: 50%; animation: float 6s ease-in-out infinite; }
        .glow-orb-1 { width: 300px; height: 300px; top: 20%; left: 10%; background: radial-gradient(circle, rgba(255, 71, 87, 0.05), transparent 70%); }
        .glow-orb-2 { width: 250px; height: 250px; bottom: 20%; right: 10%; background: radial-gradient(circle, rgba(255, 71, 87, 0.05), transparent 70%); animation-delay: -3s; }
        
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        /* เอฟเฟกต์สั่นตอนเปิดหน้าเว็บเพื่อเตือนว่าผิด */
        @keyframes shake { 
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</head>
<body>

    <div class="background-effects">
        <div class="glow-orb glow-orb-1"></div>
        <div class="glow-orb glow-orb-2"></div>
    </div>

    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">✕</div>
            <h2>LOGIN FAILED</h2>
            <p><?php echo $error_message; ?></p>
            
            <a href="login.php" class="back-btn">กลับไปลองอีกครั้ง</a>
        </div>
    </div>

</body>
</html>