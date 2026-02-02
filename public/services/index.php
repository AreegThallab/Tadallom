<?php
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>خدماتنا - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/../dashboard/dashboard_header.php"; ?>

<main class="services-dark" id="services">
  <div class="container page-top">

    <div class="services-head">
      <h1 class="services-title">خدماتنا</h1>
      <p class="services-sub">
        استكشف أبرز الخدمات التي نوفرها لك لبناء تظلّمك بشكل صحيح،
        <span class="services-accent">وتسهيل رحلتك الإجرائية</span>.
      </p>

      <div class="btns" style="justify-content:center;margin-top:14px;">
        <a class="btn primary" href="/tazallom_mvp/public/requests/create.php">رفع طلب جديد</a>
        <a class="btn" href="/tazallom_mvp/public/requests/index.php">الطلبات</a>
      </div>
    </div>

    <div class="services-grid">

      <div class="service-card">
        <div class="service-icon" aria-hidden="true">
          <!-- ميزان -->
          <svg viewBox="0 0 24 24">
            <path d="M12 3v18"></path>
            <path d="M7 21h10"></path>
            <path d="M6 7h12"></path>
            <path d="M6 7l-2 4a4 4 0 0 0 4 2"></path>
            <path d="M18 7l2 4a4 4 0 0 1-4 2"></path>
          </svg>
        </div>
        <h3>تحديد المسار</h3>
        <p>تحديد نوع القرار والجهة، ثم حساب المهلة النظامية تلقائيًا حسب قواعد الجهة والقرار.</p>
      </div>

      <div class="service-card">
        <div class="service-icon" aria-hidden="true">
          <!-- وثيقة -->
          <svg viewBox="0 0 24 24">
            <path d="M7 3h7l3 3v15H7z"></path>
            <path d="M14 3v4h4"></path>
            <path d="M9 12h6"></path>
            <path d="M9 16h6"></path>
          </svg>
        </div>
        <h3>تحليل سياقي</h3>
        <p>تحليل نص التظلّم وتصنيف الحالة (وظيفي/مالي/بلدي/تعليمي…) لإظهار الصورة الإجرائية.</p>
      </div>

      <div class="service-card">
        <div class="service-icon" aria-hidden="true">
          <!-- مطرقة -->
          <svg viewBox="0 0 24 24">
            <path d="M14 6l4 4"></path>
            <path d="M12 8l4 4"></path>
            <path d="M3 21l8-8"></path>
            <path d="M9 5l5 5"></path>
            <path d="M2 22h8"></path>
          </svg>
        </div>
        <h3>تجهيز التقرير</h3>
        <p>تقرير إجرائي جاهز للطباعة يدعم PDF ويمكن استخدامه كدليل ضمن الدعوى.</p>
      </div>

      <div class="service-card">
        <div class="service-icon" aria-hidden="true">
          <!-- ساعة -->
          <svg viewBox="0 0 24 24">
            <path d="M12 8v5l3 2"></path>
            <path d="M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18"></path>
          </svg>
        </div>
        <h3>متابعة المهلة</h3>
        <p>متابعة “المتبقي” حتى نهاية المهلة، وتنبيهك عند اقتراب آخر يوم.</p>
      </div>

      <div class="service-card">
        <div class="service-icon" aria-hidden="true">
          <!-- درع -->
          <svg viewBox="0 0 24 24">
            <path d="M12 3l8 4v6c0 5-3.5 8.5-8 10c-4.5-1.5-8-5-8-10V7z"></path>
            <path d="M9 12l2 2l4-5"></path>
          </svg>
        </div>
        <h3>اكتمال المتطلبات</h3>
        <p>التحقق من اكتمال الحقول والمرفقات والبيانات قبل اعتماد الطلب وإصدار التقرير.</p>
      </div>

      <div class="service-card">
        <div class="service-icon" aria-hidden="true">
          <!-- تواصل -->
          <svg viewBox="0 0 24 24">
            <path d="M21 11a8 8 0 1 1-3-6"></path>
            <path d="M21 3v6h-6"></path>
            <path d="M8 10h8"></path>
            <path d="M8 14h5"></path>
          </svg>
        </div>
        <h3>دعم وتواصل</h3>
        <p>قناة واضحة لتحديث رد الجهة (محاكاة للمسابقة) وتسجيل “لم يتم الرد”.</p>
      </div>

    </div>
  </div>
</main>

</body>
</html>
