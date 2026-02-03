<?php require_once __DIR__ . "/helpers.php"; ?>

<nav class="landing-nav">
  <div class="container landing-wrap">

    <!-- Brand -->
    <a class="landing-brand" href="<?= base_url('index.php') ?>">
      <img src="<?= base_url('assets/logo.svg') ?>" alt="تظلُّم">
    </a>

    <!-- Links -->
    <div class="landing-links">
      <a href="<?= base_url('index.php') ?>" class="is-active">الصفحة الرئيسية</a>
      <a href="<?= base_url('index.php#features') ?>">المميزات</a>
      <a href="<?= base_url('index.php#services') ?>">خدماتنا</a>
      <a href="<?= base_url('index.php#how') ?>">كيف يعمل؟</a>
      <a href="<?= base_url('index.php#contact') ?>">تواصل معنا</a>
    </div>

    <!-- Actions -->
    <div class="landing-actions">
      <?php if (is_logged_in()): ?>
        <a class="btn" href="<?= base_url('dashboard/index.php') ?>">لوحة التحكم</a>
        <a class="btn primary" href="<?= base_url('auth/logout.php') ?>">تسجيل الخروج</a>
      <?php else: ?>
        <a class="btn" href="<?= base_url('auth/login.php') ?>">تسجيل الدخول</a>
        <a class="btn primary" href="<?= base_url('auth/register.php') ?>">إنشاء حساب</a>
      <?php endif; ?>
    </div>

  </div>
</nav>
