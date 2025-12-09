<?php
require_once 'config/Database.php';

$database = new Database();
$db = $database->getConnection();

$email = 'admin@bloodlink.com';

$query = "SELECT password FROM users WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();
$row = $stmt->fetch();

if($row) {
    echo "Password in DB: " . $row['password'] . "<br><br>";
    
    // Test with plain text
    if($row['password'] === 'password123') {
        echo "❌ Password is stored as PLAIN TEXT!<br>";
        echo "🔧 You need to hash it!<br>";
    }
    
    // Test with hash
    if(password_verify('password123', $row['password'])) {
        echo "✅ Password is correctly HASHED!<br>";
        echo "✅ Login should work now!<br>";
    }
} else {
    echo "User not found!";
}
?>
```

---

## 📊 مقارنة:

| الحالة | كلمة المرور في DB | نتيجة password_verify() |
|--------|-------------------|------------------------|
| ❌ **القديم** | `password123` | ❌ FALSE (فشل!) |
| ✅ **الجديد** | `$2y$10$92IXU...` | ✅ TRUE (نجح!) |

---

## 🎯 ملخص المشكلة:
```
المشكلة: كلمات المرور مخزنة كنص عادي ❌
الحل: تشفيرها باستخدام password_hash() ✅
النتيجة: تسجيل الدخول يعمل الآن! 🎉