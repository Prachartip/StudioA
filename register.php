<?php
include('config.php');

if (isset($_POST['register'])) { 
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // 1. เช็คชื่อซ้ำในฐานข้อมูล
    $check_dup = "SELECT * FROM users WHERE username = '$username'";
    $result_dup = mysqli_query($conn, $check_dup);

    if (mysqli_num_rows($result_dup) > 0) {
        echo "<script>alert('ชื่อผู้ใช้นี้มีคนใช้แล้ว กรุณาตั้งชื่อใหม่ครับ!'); window.history.back();</script>";
    } else {
        // 2. ถ้าไม่ซ้ำ ให้บันทึกข้อมูล
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        
        if (mysqli_query($conn, $sql)){
            echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); window.location='login.php';</script>"; 
        } else {
            echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - STUDIO A</title>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Kanit:wght@300;400;500;600&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Chakra Petch', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0f; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
            overflow: hidden;
        }
        
        /* สไตล์โลโก้ที่แก้แล้ว */
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
        .login-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, #00ff88, transparent); 
            opacity: 0; transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .login-card:hover::before { opacity: 1; }
        .login-card:hover {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(0, 255, 136, 0.1), 0 0 40px rgba(0, 255, 136, 0.1);
            transform: translateY(-2px);
        }
        .login-header { text-align: center; margin-bottom: 40px; }
        
        .login-header h2 { 
            color: #ffffff; 
            font-size: 2.2rem; 
            font-weight: 700; 
            margin-bottom: 8px; 
            letter-spacing: 2px; 
        }
        .login-header p { color: #b0b0c0; font-size: 16px; font-weight: 400; }
        
        .form-group { margin-bottom: 24px; position: relative; }
        .input-wrapper { position: relative; margin-bottom: 24px; }
        .input-wrapper input {
            width: 100%; background: #1a1a25; border: 1px solid #2a2a35; border-radius: 6px;
            padding: 24px 16px 8px 16px;
            color: #ffffff; 
            font-size: 16px; font-weight: 400; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;
        }
        .input-wrapper input:focus {
            border-color: #00ff88; background: rgba(26, 26, 37, 0.8);
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1), 0 4px 20px rgba(0, 255, 136, 0.1);
        }
        .input-wrapper input:focus + label, .input-wrapper input:valid + label {
            transform: translateY(-12px) scale(0.85);
            color: #00ff88; font-weight: 500;
        }
        .input-wrapper label {
            position: absolute; left: 16px; top: 16px; color: #a0a0b0; font-size: 16px; font-weight: 400;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none; transform-origin: left top;
        }
        .input-line {
            position: absolute; bottom: 0; left: 50%; width: 0; height: 2px;
            background: linear-gradient(90deg, #00ff88, #0099ff); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); transform: translateX(-50%); border-radius: 2px;
        }
        .input-wrapper input:focus ~ .input-line { width: 100%; }
        
        .login-btn {
            width: 100%; background: linear-gradient(135deg, #00ff88, #0099ff); border: none; border-radius: 6px;
            padding: 16px 32px; 
            color: #0a0a0f; 
            font-size: 16px; font-weight: 600; cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .btn-glow {
            position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); transition: left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .login-btn:hover {
            transform: translateY(-1px); box-shadow: 0 10px 30px rgba(0, 255, 136, 0.3), 0 0 40px rgba(0, 255, 136, 0.2);
        }
        .login-btn:hover .btn-glow { left: 100%; }
        
        .signup-link { text-align: center; }
        .signup-link p { color: #a0a0b0; font-size: 14px; }
        .signup-link a { 
            color: #b0b0c0; 
            text-decoration: none; font-weight: 500; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .signup-link a:hover { 
            color: #0099ff; 
            text-shadow: 0 0 10px rgba(0, 153, 255, 0.5); 
        }
        
        /* Background Effects */
        .background-effects { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
        .glow-orb { position: absolute; border-radius: 50%; background: radial-gradient(circle, rgba(0, 255, 136, 0.1), transparent 70%); animation: float 6s ease-in-out infinite; }
        .glow-orb-1 { width: 300px; height: 300px; top: 10%; left: -10%; background: radial-gradient(circle, rgba(0, 255, 136, 0.08), transparent 70%); animation-delay: 0s; }
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
                <h2>SIGN UP</h2>
                <p>create a new account</p>
            </div>

            <form action="" method="POST">
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="username" required>
                        <label>ตั้งชื่อผู้ใช้ (Username)</label>
                        <div class="input-line"></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" name="password" required>
                        <label>ตั้งรหัสผ่าน (Password)</label>
                        <div class="input-line"></div>
                    </div>
                </div>

                <button type="submit" name="register" class="login-btn">
                    <span class="btn-text">สร้างบัญชี</span>
                    <div class="btn-glow"></div>
                </button>

            </form>

            <div class="signup-link">
                <p>มีบัญชีอยู่แล้วใช่ไหม? <a href="login.php">เข้าสู่ระบบที่นี่</a></p>
            </div>

        </div>
    </div>

</body>
</html>