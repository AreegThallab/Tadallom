<?php require_once __DIR__ . "/../includes/helpers.php"; ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تظلُّم</title>
  <link rel="stylesheet" href="/assets/styles.css" />
</head>
<body>

  <?php require_once __DIR__ . "/../includes/layout_header.php"; ?>

  <main class="container page-top">

    <div class="glass card reveal" data-parallax="10">
      <div class="grid">

        <!-- Text -->
        <div class="reveal">
          <div class="h1"> العدالة تبدأ وعيًا قبل أن تُنطق حكمًا</div>
          <p class="p">
            نظام ذكي يساعدك تبني تظلّمك الصحيح؛ من إدخال القرار، إلى فهم حالتك، وصولًا إلى تقرير نهائي واضح وجاهز
          </p>

          <div class="btns" style="margin-top:16px">
            <a class="btn primary" href="/auth/register.php">ابدأ الآن</a>
            <a class="btn" href="#features">وش يميّزنا؟</a>
          </div>
        </div>

        <!-- Logo big card -->
        <div class="glass card hero-card reveal" data-parallax="6">
          <div style="display:grid;gap:10px;place-items:center">
            <div style="width:220px;height:220px;display:grid;place-items:center;border-radius:28px;background:transparent;border:0;">
              <img
                src="/assets/logo1.svg"
                alt="شعار تظلُّم"
                style="width:100%;height:100%;object-fit:contain;padding:0;filter:none;"
              >
            </div>

            <div class="btns" style="justify-content:center">
              <a class="btn primary" href="/auth/register.php">إنشاء حساب</a>
              <a class="btn" href="/auth/login.php">تسجيل دخول</a>
            </div>
          </div>
        </div>

      </div>
    </div>

  </main>

  <!-- ✅ FEATURES (فاتح) -->
  <section class="section-light" id="features">
    <div class="container">
      <div class="features-grid">

        <div class="feature-card js-reveal">
          <div class="icon-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M7 3h7l3 3v15H7z"></path>
              <path d="M14 3v4h4"></path>
              <path d="M9 12h6"></path>
              <path d="M9 16h6"></path>
            </svg>
          </div>
          <h3>منصة موحدة</h3>
          <p>كل ما تحتاجه من إجراءات في مكان واحد.</p>
        </div>

        <div class="feature-card js-reveal">
          <div class="icon-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M14 6l4 4"></path>
              <path d="M12 8l4 4"></path>
              <path d="M3 21l8-8"></path>
              <path d="M9 5l5 5"></path>
              <path d="M2 22h8"></path>
            </svg>
          </div>
          <h3>تسريع الإنجاز</h3>
          <p>وفّر وقتك والجهد في المتابعة من خلال مسار واضح.</p>
        </div>

        <div class="feature-card js-reveal">
          <div class="icon-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M12 3v18"></path>
              <path d="M7 21h10"></path>
              <path d="M6 7h12"></path>
              <path d="M6 7l-2 4a4 4 0 0 0 4 2"></path>
              <path d="M18 7l2 4a4 4 0 0 1-4 2"></path>
            </svg>
          </div>
          <h3>ضمان الالتزام</h3>
          <p>وسيط موثوق بينك وبين الجهة المعنية لضمان جودة التنفيذ.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ✅ SERVICES (غامق) -->
  <section class="services-dark" id="services">
    <div class="container">
      <div class="services-head">
        <h2 class="services-title">تعرّف على خدماتنا</h2>
        <p class="services-sub">
          استكشف أبرز الخدمات التي نوفرها لك لبناء تظلّمك بشكل صحيح،
          <span class="services-accent">وتسهيل رحلتك الإجرائية</span>.
        </p>
      </div>

      <div class="services-grid">

        <div class="service-card">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M12 3v18"></path>
              <path d="M7 21h10"></path>
              <path d="M6 7h12"></path>
              <path d="M6 7l-2 4a4 4 0 0 0 4 2"></path>
              <path d="M18 7l2 4a4 4 0 0 1-4 2"></path>
            </svg>
          </div>
          <h3>تحديد المسار</h3>
          <p>نقترح نوع التظلّم والمسار المناسب حسب قرارك بشكل واضح.</p>
        </div>

        <div class="service-card">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M7 3h7l3 3v15H7z"></path>
              <path d="M14 3v4h4"></path>
              <path d="M9 12h6"></path>
              <path d="M9 16h6"></path>
            </svg>
          </div>
          <h3>تحليل سياقي</h3>
          <p>تلخيص النقاط المهمة وفهم الحالة عشان ما تضيع التفاصيل.</p>
        </div>

        <div class="service-card">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M14 6l4 4"></path>
              <path d="M12 8l4 4"></path>
              <path d="M3 21l8-8"></path>
              <path d="M9 5l5 5"></path>
              <path d="M2 22h8"></path>
            </svg>
          </div>
          <h3>تجهيز التقرير</h3>
          <p>ننتج تقرير واضح وجاهز للطباعة وفق المتطلبات.</p>
        </div>

        <div class="service-card">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M12 8v5l3 2"></path>
              <path d="M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18"></path>
            </svg>
          </div>
          <h3>تحديد المده الزمنية المطلوبه</h3>
          <p>متابعة التقديم والردود + خيار “ما تم الرد عليه”.</p>
        </div>

        <div class="service-card">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M12 3l8 4v6c0 5-3.5 8.5-8 10c-4.5-1.5-8-5-8-10V7z"></path>
              <path d="M9 12l2 2l4-5"></path>
            </svg>
          </div>
          <h3>اكتمال المتطلبات</h3>
          <p>نتأكد من اكتمال البيانات قبل ما يطلع تقريرك النهائي.</p>
        </div>

        <div class="service-card">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
              <path d="M21 11a8 8 0 1 1-3-6"></path>
              <path d="M21 3v6h-6"></path>
              <path d="M8 10h8"></path>
              <path d="M8 14h5"></path>
            </svg>
          </div>
          <h3>تواصل ودعم</h3>
          <p>قناة واضحة للتواصل والمتابعة أثناء بناء تظلّمك.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ✅ HOW (فاتح) -->
  <section class="how-light" id="how">
    <div class="container">
      <div class="how-card reveal">
        <h3 style="margin:0;font-size:16px;color:var(--light-text)">كيف يعمل؟</h3>
        <p class="p" style="margin-top:6px;max-width:900px;color:var(--light-muted)">
          1) إدخال بيانات القرار والتظلّم → 2) تحليل السياق → 3) تحديد المسار →
          4) متابعة مهلة 60 يوم → 5) التحقق من اكتمال الإجراء → 6) تقرير نهائي.
        </p>
      </div>
    </div>
  </section>

  <?php require_once __DIR__ . "/../includes/layout_footer.php"; ?>

  <script src="/assets/app.js"></script>
</body>
</html>
