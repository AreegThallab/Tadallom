<?php require_once __DIR__ . "/helpers.php"; ?>

<nav class="landing-nav">
  <div class="container landing-wrap">

    <!-- Brand -->
    <a class="landing-brand" href="/tazallom_mvp/public/index.php">
      <img src="/assets/logo.svg
" alt="تظلُّم">
    </a>

    <!-- Links -->
    <div class="landing-links">
      <a href="/tazallom_mvp/public/index.php" class="is-active">الصفحة الرئيسية</a>
      <a href="/tazallom_mvp/public/index.php#features">المميزات</a>
      <a href="/tazallom_mvp/public/index.php#services">خدماتنا</a>

      <a href="/tazallom_mvp/public/index.php#how">كيف يعمل؟</a>
      <a href="/tazallom_mvp/public/index.php#contact">تواصل معنا</a>
    </div>

    <!-- Actions -->
    <div class="landing-actions">
      <?php if (is_logged_in()): ?>
      <a class="btn" href="/tazallom_mvp/public/auth/login.php">تسجيل الدخول</a>
      <a class="btn primary" href="/tazallom_mvp/public/auth/register.php">إنشاء حساب</a>
      <?php else: ?>
        <a class="btn" href="/tazallom_mvp/public/auth/login.php">تسجيل الدخول</a>
        <a class="btn primary" href="/tazallom_mvp/public/auth/register.php">إنشاء حساب</a>
      <?php endif; ?>
    </div>

  </div>
</nav>
