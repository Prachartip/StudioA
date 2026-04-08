<?php
include('config.php');

if (isset($_POST['reset_password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = $_POST['new_password'];

    // เช็คว่ามีชื่อผู้ใช้นี้ในระบบหรือไม่
    $check_user = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_user);

    if (mysqli_num_rows($result) > 0) {
        // ถ้ามี ให้ทำการอัปเดตรหัสผ่านใหม่
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashed_password' WHERE username = '$username'";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('เปลี่ยนรหัสผ่านสำเร็จ! กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        // ถ้าไม่พบชื่อผู้ใช้
        echo "<script>alert('ไม่พบชื่อผู้ใช้นี้ในระบบ กรุณาตรวจสอบอีกครั้ง!'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน - STUDIO A</title>
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
        
        /* สไตล์โลโก้ */
        .custom-logo {
            width: 120px;
            height: auto;
            margin-bottom: 24px; 
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(255,255,255, 0.1);
        }
        
        .login-container { width: 100%; max-width: 400px; position: relative; z-index: 10; }
        .login-card {
            background: #151520; 
            border: 1px solid #2a2a35; border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative; backdrop-filter: blur(20px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* เปลี่ยนเส้นขอบบนให้เป็นสีส้ม/แดง เพื่อสื่อถึงการแจ้งเตือน/ลืมรหัส */
        .login-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, #ff4757, transparent); 
            opacity: 0; transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .login-card:hover::before { opacity: 1; }
        .login-card:hover {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 71, 87, 0.1), 0 0 40px rgba(255, 71, 87, 0.1);
            transform: translateY(-2px);
        }
        
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h2 { 
            font-family: 'Chakra Petch', sans-serif;
            color: #ffffff; 
            font-size: 2rem; 
            font-weight: 700; 
            margin-bottom: 8px; 
            letter-spacing: 1px; 
        }
        .login-header p { color: #b0b0c0; font-size: 15px; font-weight: 400; }
        
        .form-group { margin-bottom: 24px; position: relative; }
        .input-wrapper { position: relative; margin-bottom: 24px; }
        .input-wrapper input {
            width: 100%; background: #1a1a25; border: 1px solid #2a2a35; border-radius: 6px;
            padding: 24px 16px 8px 16px;
            color: #ffffff; 
            font-size: 16px; font-family: 'Kanit', sans-serif; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;
        }
        
        /* เปลี่ยนสีตอนพิมพ์ให้เป็นสีส้มอมแดง */
        .input-wrapper input:focus {
            border-color: #ff4757; background: rgba(26, 26, 37, 0.8);
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
        }
        .input-wrapper input:focus + label, .input-wrapper input:valid + label {
            transform: translateY(-12px) scale(0.85);
            color: #ff4757; font-weight: 500;
        }
        .input-wrapper label {
            position: absolute; left: 16px; top: 16px; color: #a0a0b0; font-size: 16px; font-weight: 400;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none; transform-origin: left top;
        }
        
        .login-btn {
            width: 100%; 
            /* ปุ่มเปลี่ยนเป็นโทนแดง/ส้ม เพื่อให้แตกต่างจากหน้า Login/Register */
            background: linear-gradient(135deg, #ff4757, #ff6b81); 
            border: none; border-radius: 6px;
            padding: 16px 32px; 
            color: #ffffff; 
            font-size: 16px; font-weight: 600; cursor: pointer; font-family: 'Kanit', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .login-btn:hover {
            transform: translateY(-1px); box-shadow: 0 10px 30px rgba(255, 71, 87, 0.3);
        }
        
        .signup-link { text-align: center; }
        .signup-link a { 
            color: #b0b0c0; 
            text-decoration: none; font-weight: 500; font-size: 15px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .signup-link a:hover { 
            color: #ffffff; 
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5); 
        }
        
        /* Background Effects */
        .background-effects { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
        .glow-orb { position: absolute; border-radius: 50%; background: radial-gradient(circle, rgba(0, 255, 136, 0.1), transparent 70%); animation: float 6s ease-in-out infinite; }
        .glow-orb-1 { width: 300px; height: 300px; top: 10%; left: -10%; background: radial-gradient(circle, rgba(255, 71, 87, 0.08), transparent 70%); animation-delay: 0s; }
        .glow-orb-2 { width: 200px; height: 200px; top: 60%; right: -5%; background: radial-gradient(circle, rgba(0, 153, 255, 0.06), transparent 70%); animation-delay: -2s; }
        .glow-orb-3 { width: 150px; height: 150px; bottom: 20%; left: 10%; background: radial-gradient(circle, rgba(255, 0, 128, 0.04), transparent 70%); animation-delay: -4s; }
        @keyframes float { 0%, 100% { transform: translateY(0px) translateX(0px); } 33% { transform: translateY(-20px) translateX(10px); } 66% { transform: translateY(10px) translateX(-10px); } }
    </style>
</head>
<body>

    <div class="background-effects">
        <div class="glow-orb glow-orb-1"></div>
        <div class="glow-orb glow-orb-2"></div>
        <div class="glow-orb glow-orb-3"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            
            <div class="login-header">
                <img src="NewLogo.png" class="custom-logo" alt="STUDIO A Logo">
                <h2>RESET PASSWORD</h2>
                <p>กู้คืนการเข้าถึงบัญชีของคุณ</p>
            </div>

            <form action="" method="POST">
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="username" required>
                        <label>ชื่อผู้ใช้ที่ต้องการเปลี่ยนรหัส</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" name="new_password" required>
                        <label>ตั้งรหัสผ่านใหม่ (New Password)</label>
                    </div>
                </div>

                <button type="submit" name="reset_password" class="login-btn">
                    ยืนยันการเปลี่ยนรหัสผ่าน
                </button>

            </form>

            <div class="signup-link">
                <a href="login.php">← กลับไปหน้าเข้าสู่ระบบ</a>
            </div>

        </div>
    </div>

</body>
</html>