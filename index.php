<?php
session_start();

// แก้จาก user_id เป็น username ให้ตรงกับระบบ Login ที่เราทำไว้ครับ
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUDIO A - Art Toy</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* =========================================
           ธีม Neon Minimalist (Dark Mode)
           ========================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #0a0a0f; /* พื้นหลังดำเข้ม */
            color: #ffffff; /* ตัวหนังสือสีขาว */
            line-height: 1.6;
        }

        /* --- แถบเมนูด้านบน (Glassmorphism) --- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(21, 21, 32, 0.8); /* สีเทาโปร่งแสง */
            backdrop-filter: blur(15px); /* เบลอพื้นหลัง */
            border-bottom: 1px solid #2a2a35;
            padding: 15px 40px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        header img {
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }
        nav {
            display: flex;
            gap: 15px;
        }
        nav button {
            background-color: transparent;
            color: #a0a0b0;
            border: 1px solid transparent;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s ease;
        }
        /* เอฟเฟกต์ตอนเอาเมาส์ชี้ปุ่มเมนู */
        nav button:hover {
            color: #00ff88;
            border-color: #00ff88;
            background: rgba(0, 255, 136, 0.05);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
        }
        
        /* ปุ่มโปรไฟล์ให้โดดเด่นนิดนึง */
        nav button:last-child {
            background: linear-gradient(135deg, #0099ff, #00ff88);
            color: #0a0a0f;
            font-weight: 600;
            border: none;
        }
        nav button:last-child:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.3);
            color: #0a0a0f;
        }
        /* =========================================
           ระบบ Dropdown Menu
           ========================================= */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        /* ปุ่มหลักของ Dropdown */
        .dropbtn {
            background-color: transparent;
            color: #a0a0b0;
            border: 1px solid transparent;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s ease;
        }
        
        /* กล่องเมนูที่จะหล่นลงมา */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #151520; /* สีเทาเข้มให้กลืนกับ Glassmorphism */
            min-width: 180px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid #2a2a35;
            border-radius: 12px;
            z-index: 1000;
            top: 100%;
            left: 0;
            margin-top: 10px; /* ระยะห่างจากปุ่มหลัก */
            overflow: hidden;
        }

        /* ลิงก์ด้านใน Dropdown */
        .dropdown-content a {
            color: #a0a0b0;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: all 0.3s ease;
            border-bottom: 1px solid #2a2a35;
        }
        
        /* ลบเส้นใต้ของเมนูอันสุดท้าย */
        .dropdown-content a:last-child {
            border-bottom: none;
        }

        /* เอฟเฟกต์เวลาเอาเมาส์ชี้ที่ลิงก์ย่อย (เรืองแสงและขยับนิดๆ) */
        .dropdown-content a:hover {
            background-color: rgba(0, 255, 136, 0.1);
            color: #00ff88;
            padding-left: 28px; 
        }
        /* ✨ เพิ่มคลาส .show สำหรับให้ JS สั่งโชว์เมนู */
        .dropdown-content.show {
            display: block;
            animation: dropFade 0.2s ease forwards;
        }
        
        /* สีกดค้างของปุ่มหลัก */
        .dropbtn.active {
            color: #00ff88;
        }

        @keyframes dropFade {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- รูปแบนเนอร์ใหญ่ --- */
        #home {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0a0a0f; 
            padding: 40px 20px;
            border-bottom: 1px solid #2a2a35;
        }
        #home img {
            max-width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* --- ส่วนเนื้อหาและตารางสินค้า --- */
        .container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .section-title {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
            color: #ffffff;
            letter-spacing: 1px;
            text-shadow: 0 0 20px rgba(0, 153, 255, 0.3);
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        
        /* การ์ดสินค้า */
        .grid-item {
            background-color: #151520; /* สีเทาเข้มแบบกล่อง Login */
            border: 1px solid #2a2a35;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .grid-item::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, #0099ff, transparent); 
            opacity: 0; transition: opacity 0.3s;
        }
        .grid-item:hover::before { opacity: 1; }
        .grid-item:hover {
            transform: translateY(-5px);
            border-color: #0099ff;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 153, 255, 0.15);
        }
        
        .grid-item img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .grid-item h2 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #ffffff;
        }
        .grid-item p {
            color: #00ff88; /* วันที่สีเขียวนีออน */
            font-family: 'Chakra Petch', sans-serif;
            font-size: 14px;
        }

        /* --- ส่วนประวัติ --- */
        .history {
            text-align: center; 
            margin: 80px auto; 
            padding: 40px;
            max-width: 800px; 
            background: #151520;
            border: 1px solid #2a2a35;
            border-radius: 16px;
        }
        .history h1 {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 24px;
            color: #0099ff;
            margin-bottom: 15px;
        }
        .history h2 { margin-bottom: 15px; font-size: 20px; color: #ffffff; }
        .history p { color: #a0a0b0; font-size: 16px; }

        /* --- Footer --- */
        footer {
            text-align: center;
            background-color: #050508;
            border-top: 1px solid #2a2a35;
            color: #a0a0b0;
            padding: 20px;
            font-size: 14px;
            font-family: 'Chakra Petch', sans-serif;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    
    <header>
        <img src="NewLogo.png" alt="STUDIOA Logo" style="filter: drop-shadow(0 0 10px rgba(255,255,255,0.2));">
        <nav>
            <button onclick="window.location.href='index.php'">หน้าหลัก</button>
            
            <div class="dropdown">
                <button class="dropbtn" onclick="toggleDropdown()">ประวัติ Art Toy ▾</button>
                
                <div class="dropdown-content" id="myDropdown">
                    <a href="bearbrick.html">Bearbrick</a>
                    <a href="kaws.html">Kaws</a>
                    <a href="crybaby.html">Cry Baby</a>
                    <a href="skullpanda.html">SkullPanda</a>
                </div>
            </div>

            <button onclick="window.location.href='products.html'">ดูสินค้าทั้งหมด</button>
            <button onclick="window.location.href='profile.php'">โปรไฟล์</button>
        </nav>
    </header>

    <section id="home">
        <img src="https://i.imgur.com/I8cGOvQ.png" alt="Banner STUDIO A">
    </section>
    
    <div class="container">
        <h1 class="section-title">อัปเดตข่าวสารเกี่ยวกับ Art Toy</h1>
        <div class="grid">
            <div class="grid-item">
                <img src="https://i.imgur.com/uef8tFG.png" alt="LABUBU Hip-hop Girl Figure">
                <h2>LABUBU Hip-hop Girl Figure</h2>
                <p>06/06/2024</p>
            </div>
            <div class="grid-item">
                <img src="https://www.tclub.com.hk/cdn/shop/files/4530956618739_01.jpg?v=1735036104" alt="BE@RBRICK KEVIN COSTUME Ver. 400%">
                <h2>BE@RBRICK KEVIN COSTUME Ver. 400%</h2>
                <p>12/01/2025</p>
            </div>
            <div class="grid-item">
                <img src="https://i.imgur.com/sni5JMK.png" alt="CRYBABY×Powerpuff Girls Series Figures">
                <h2>CRYBABY×Powerpuff Girls Series Figures</h2>
                <p>08/03/2024</p>
            </div>
            <div class="grid-item">
                <img src="https://i.imgur.com/Ng2V2zy.png" alt="MOLLY – IT Mini figure">
                <h2>MOLLY – IT Mini figure</h2>
                <p>03/01/2025</p>
            </div>
        </div>
    </div>

    <div class="history">
        <h1>ประวัติของ Art Toy</h1>
        <h2><strong>Art Toy ของเล่นที่เหมือนงานศิลปะ</strong></h2>
        <p>ก่อนอื่น เราจะอธิบายให้เข้าใจตรงกันก่อนว่า Art Toy คืออะไร Art Toy หรือที่ฝั่งตะวันตกจะเรียกว่า Designer Toy คือของเล่นในรูปแบบศิลปะ ที่ออกแบบโดยศิลปิน มักถูกผลิตในจำนวนจำกัดเนื่องจากศิลปินเป็นผู้ผลิตเอง นั่นจึงเป็นสาเหตุที่ทำให้ Art Toy บางชิ้นมีราคาสูงถึงหนึ่งแสนบาท นั่นก็เพราะจำนวนที่จำกัด และถูกออกแบบโดยศิลปินนั่นเอง</p>
    </div>
          
    <footer>
        <p>© 2025 STUDIO A. All Rights Reserved.</p>
    </footer>

    <script>
        // ฟังก์ชันสั่งกาง-หุบ เมนู
        function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
            document.querySelector(".dropbtn").classList.toggle("active");
        }

        // ฟังก์ชันความฉลาด: ถ้าคลิกพื้นที่อื่นบนหน้าจอ ให้หุบเมนูเก็บอัตโนมัติ
        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var dropbtn = document.querySelector(".dropbtn");
                
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                        if(dropbtn) dropbtn.classList.remove('active');
                    }
                }
            }
        }
    </script>

</body>
</html>