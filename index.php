<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ลงทะเบียนใบหน้านักศึกษา</title>
    <style>
        body { font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; cursor: pointer; }
        .msg { padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h2>ลงทะเบียนระบบเช็คชื่อ</h2>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $student_name = trim($_POST['fullname']);
        // แทนที่ช่องว่างด้วย underscore เพื่อไม่ให้ Python งงตอนอ่าน Path
        $folder_name = str_replace(' ', '_', $student_name);
        $target_dir = "dataset/" . $folder_name . "/";

        // 1. สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . "profile." . $file_extension;

        // 2. ตรวจสอบประเภทไฟล์
        $allowed_types = array("jpg", "jpeg", "png");
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                echo "<div class='msg success'>ลงทะเบียนสำเร็จ! ยินดีต้อนรับคุณ $student_name</div>";
            } else {
                echo "<div class='msg error'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์</div>";
            }
        } else {
            echo "<div class='msg error'>กรุณาอัปโหลดไฟล์ JPG หรือ PNG เท่านั้น</div>";
        }
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label>ชื่อ-นามสกุล (ภาษาอังกฤษ):</label>
        <input type="text" name="fullname" placeholder="เช่น Somsak_Saetang" required>
        
        <label>เลือกรูปหน้าตรง:</label>
        <input type="file" name="photo" accept="image/*" required>
        
        <button type="submit">ส่งข้อมูลลงทะเบียน</button>
    </form>

</body>
</html>
