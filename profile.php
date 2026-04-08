<?php
session_start();
include('config.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$current_username = $_SESSION['username'];

// 1. ดึงข้อมูลปัจจุบันของผู้ใช้มาแสดงในฟอร์ม
$query = "SELECT * FROM users WHERE username = '$current_username'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// เมื่อกดปุ่ม "บันทึกการเปลี่ยนแปลง"
if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = $_POST['new_password'];
    
    // ข้อมูลจัดส่งที่เพิ่มเข้ามาใหม่
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // เช็คชื่อซ้ำ (ยกเว้นชื่อตัวเอง)
    $check_dup = "SELECT * FROM users WHERE username = '$new_username' AND username != '$current_username'";
    $result_dup = mysqli_query($conn, $check_dup);

    if (mysqli_num_rows($result_dup) > 0) {
        echo "<script>alert('ชื่อผู้ใช้นี้มีคนอื่นใช้แล้ว กรุณาเลือกชื่ออื่นครับ!');</script>";
    } else {
        // อัปเดตข้อมูล (เช็คว่ามีการเปลี่ยนรหัสผ่านไหม)
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET 
                    username = '$new_username', 
                    password = '$hashed_password',
                    full_name = '$full_name',
                    phone = '$phone',
                    address = '$address'
                    WHERE username = '$current_username'";
        } else {
            $sql = "UPDATE users SET 
                    username = '$new_username',
                    full_name = '$full_name',
                    phone = '$phone',
                    address = '$address'
                    WHERE username = '$current_username'";
        }

        if (mysqli_query($conn, $sql)) {
            $_SESSION['username'] = $new_username; // อัปเดต Session
            echo "<script>alert('อัปเดตข้อมูลโปรไฟล์และที่อยู่สำเร็จ!'); window.location='profile.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการโปรไฟล์ - STUDIO A</title>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Kanit', sans-serif;
            background: #0a0a0f; 
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .back-btn {
            position: absolute; top: 30px; left: 30px;
            color: #a0a0b0; text-decoration: none; font-size: 16px;
            display: flex; align-items: center; gap: 8px; transition: 0.3s; z-index: 20;
        }
        .back-btn:hover { color: #00ff88; text-shadow: 0 0 10px rgba(0,255,136,0.5); }

        .profile-container { width: 100%; max-width: 600px; position: relative; z-index: 10; margin-top: 40px; }
        .profile-card {
            background: #151520; border: 1px solid #2a2a35; border-radius: 16px;
            padding: 40px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: relative; backdrop-filter: blur(20px);
        }
        .profile-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, #0099ff, #00ff88, transparent); 
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { font-family: 'Chakra Petch', sans-serif; color: #ffffff; font-size: 2rem; font-weight: 700; letter-spacing: 1px; }
        
        .section-title {
            color: #00ff88; font-family: 'Chakra Petch', sans-serif; font-size: 1.2rem; 
            margin: 30px 0 15px 0; padding-bottom: 5px; border-bottom: 1px solid #2a2a35;
        }

        .form-group { margin-bottom: 24px; position: relative; }
        .input-wrapper { position: relative; }
        
        /* เพิ่มสไตล์ให้รองรับ textarea (กล่องที่อยู่) */
        .input-wrapper input, .input-wrapper textarea {
            width: 100%; background: #1a1a25; border: 1px solid #2a2a35; border-radius: 6px;
            padding: 24px 16px 8px 16px; color: #ffffff; font-size: 16px; font-family: 'Kanit', sans-serif; outline: none; transition: 0.3s;
        }
        .input-wrapper textarea { resize: vertical; min-height: 100px; }
        
        .input-wrapper input:focus, .input-wrapper textarea:focus {
            border-color: #0099ff; background: rgba(26, 26, 37, 0.8); box-shadow: 0 0 0 3px rgba(0, 153, 255, 0.1);
        }
        .input-wrapper label {
            position: absolute; left: 16px; top: 16px; color: #a0a0b0; font-size: 14px; transition: 0.3s; pointer-events: none;
        }
        .input-wrapper input:focus + label, .input-wrapper input:not(:placeholder-shown) + label,
        .input-wrapper textarea:focus + label, .input-wrapper textarea:not(:placeholder-shown) + label {
            transform: translateY(-10px) scale(0.85); color: #0099ff; font-weight: 500;
        }

        .save-btn {
            width: 100%; background: linear-gradient(135deg, #0099ff, #00ff88); border: none; border-radius: 6px;
            padding: 16px; color: #0a0a0f; font-size: 16px; font-weight: 600; cursor: pointer; font-family: 'Kanit', sans-serif;
            transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; margin-top: 20px;
        }
        .save-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 153, 255, 0.3); }
        .note { text-align: center; font-size: 13px; color: #ff4757; margin-top: 15px; }
    </style>
</head>
<body>

    <a href="index.php" class="back-btn"><span>← กลับไปหน้าร้านค้า</span></a>

    <div class="profile-container">
        <div class="profile-card">
            
            <div class="header">
                <h2>MY PROFILE</h2>
            </div>

            <form action="" method="POST">
                
                <h3 class="section-title">🔒 ข้อมูลบัญชี (Account)</h3>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" placeholder=" " required>
                        <label>ชื่อผู้ใช้ (Username)</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" name="new_password" placeholder=" ">
                        <label>เปลี่ยนรหัสผ่านใหม่ (ปล่อยว่างถ้าใช้รหัสเดิม)</label>
                    </div>
                </div>

                <h3 class="section-title">📦 ข้อมูลจัดส่ง (Shipping Details)</h3>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" placeholder=" ">
                        <label>ชื่อ-นามสกุลผู้รับ</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" placeholder=" ">
                        <label>เบอร์โทรศัพท์ติดต่อ</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <textarea name="address" placeholder=" "><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                        <label>ที่อยู่สำหรับจัดส่งแบบครบถ้วน (บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์)</label>
                    </div>
                </div>

                <button type="submit" name="update_profile" class="save-btn">อัปเดตข้อมูลทั้งหมด</button>
            </form>

        </div>
    </div>

</body>
</html>