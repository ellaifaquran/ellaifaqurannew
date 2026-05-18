<?php
require_once __DIR__ . '/app/helpers.php';
$programs = active_programs();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>El-Laifa Qur'an Academy</title>
  <meta name="description" content="El-Laifa Qur'an Academy - Belajar Al-Qur'an dari dasar sampai mutqin, terarah, nyaman, dan menyenangkan." />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg-main: #f4f6db;
      --bg-soft: #fbfced;
      --primary: #4d7223;
      --primary-dark: #29470f;
      --primary-light: #7ca045;
      --accent: #e58b73;
      --accent-dark: #c66f5a;
      --gold: #d8b56e;
      --brown: #8a5a31;
      --text: #223015;
      --muted: #66715b;
      --white: #ffffff;
      --border: rgba(77, 114, 35, 0.13);
      --shadow: 0 22px 60px rgba(45, 75, 18, 0.12);
      --radius: 28px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Inter', sans-serif;
      background:
        radial-gradient(circle at top right, rgba(229, 139, 115, 0.18), transparent 34%),
        radial-gradient(circle at bottom left, rgba(124, 160, 69, 0.18), transparent 36%),
        var(--bg-main);
      color: var(--text);
      line-height: 1.7;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    img {
      max-width: 100%;
      display: block;
    }

    .container {
      width: min(1140px, calc(100% - 32px));
      margin: 0 auto;
    }

    .navbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: rgba(244, 246, 219, 0.88);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border);
    }

    .nav-inner {
      min-height: 82px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 0;
    }

    .brand-logo {
      width: 58px;
      height: 58px;
      flex: 0 0 58px;
      border-radius: 18px;
      display: grid;
      place-items: center;
      background: linear-gradient(145deg, #fbfced, #eef4c9);
      border: 1px solid rgba(77, 114, 35, 0.18);
      box-shadow: 0 10px 24px rgba(45, 75, 18, 0.10);
      overflow: hidden;
    }

    .brand-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .brand-text h1 {
      font-size: 1.15rem;
      line-height: 1.15;
      color: var(--primary-dark);
      font-weight: 800;
      letter-spacing: -0.02em;
      white-space: nowrap;
    }

    .brand-text p {
      font-size: 0.82rem;
      color: var(--muted);
      margin-top: 4px;
      white-space: nowrap;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 24px;
      font-size: 0.95rem;
      color: var(--muted);
      font-weight: 700;
    }

    .nav-links a:hover {
      color: var(--primary);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 23px;
      border-radius: 999px;
      border: none;
      cursor: pointer;
      font-weight: 800;
      text-align: center;
      transition: 0.22s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      box-shadow: 0 14px 34px rgba(45, 75, 18, 0.25);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 18px 42px rgba(45, 75, 18, 0.28);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.72);
      color: var(--primary-dark);
      border: 1px solid var(--border);
    }

    .hero {
      position: relative;
      overflow: hidden;
      padding: 88px 0 72px;
    }

    .hero-grid {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 1.08fr 0.92fr;
      align-items: center;
      gap: 54px;
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 15px;
      margin-bottom: 20px;
      border-radius: 999px;
      background: rgba(77, 114, 35, 0.10);
      border: 1px solid rgba(77, 114, 35, 0.12);
      color: var(--primary-dark);
      font-weight: 800;
      font-size: 0.91rem;
    }

    .hero h2 {
      max-width: 720px;
      margin-bottom: 20px;
      color: var(--primary-dark);
      font-size: clamp(2.55rem, 5.4vw, 5.1rem);
      line-height: 1.02;
      letter-spacing: -0.065em;
    }

    .hero h2 span {
      color: var(--accent-dark);
    }

    .hero-desc {
      max-width: 630px;
      margin-bottom: 30px;
      color: var(--muted);
      font-size: 1.08rem;
    }

    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 32px;
    }

    .hero-points {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
      max-width: 730px;
    }

    .point {
      padding: 16px;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.68);
      border: 1px solid rgba(77, 114, 35, 0.12);
      box-shadow: 0 12px 28px rgba(45, 75, 18, 0.06);
      color: var(--primary-dark);
      font-weight: 800;
    }

    .hero-panel {
      position: relative;
      min-height: 520px;
      border-radius: 34px;
      background:
        linear-gradient(150deg, rgba(255, 255, 255, 0.92), rgba(251, 252, 237, 0.88)),
        radial-gradient(circle at top right, rgba(229, 139, 115, 0.20), transparent 42%);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      padding: 32px;
      overflow: hidden;
    }

    .hero-panel::after {
      content: '';
      position: absolute;
      right: -70px;
      bottom: -90px;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      background: rgba(77, 114, 35, 0.10);
    }

    .panel-top {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 18px;
      border-radius: 24px;
      background: var(--white);
      border: 1px solid rgba(77, 114, 35, 0.10);
      box-shadow: 0 14px 30px rgba(45, 75, 18, 0.08);
      margin-bottom: 24px;
    }

    .panel-logo {
      width: 78px;
      height: 78px;
      flex: 0 0 78px;
      border-radius: 22px;
      overflow: hidden;
      background: var(--bg-soft);
      border: 1px solid rgba(77, 114, 35, 0.13);
    }

    .panel-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .panel-top h3 {
      font-size: 1.15rem;
      color: var(--primary-dark);
      line-height: 1.25;
      margin-bottom: 5px;
    }

    .panel-top p {
      color: var(--muted);
      font-size: 0.92rem;
    }

    .quote-card {
      position: relative;
      z-index: 2;
      padding: 30px;
      margin-bottom: 22px;
      border-radius: 28px;
      background: linear-gradient(145deg, #f8fae9, #fff8f1);
      border: 1px solid rgba(216, 181, 110, 0.28);
    }

    .arabic {
      margin-bottom: 12px;
      color: var(--primary-dark);
      font-family: 'Amiri', serif;
      direction: rtl;
      font-size: clamp(2rem, 4vw, 3rem);
      line-height: 1.65;
      text-align: center;
    }

    .quote-card strong {
      display: block;
      text-align: center;
      font-size: 1rem;
      color: var(--text);
    }

    .program-list {
      position: relative;
      z-index: 2;
      display: grid;
      gap: 12px;
    }

    .program-mini {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 15px 16px;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.76);
      border: 1px solid rgba(77, 114, 35, 0.10);
    }

    .program-mini span {
      color: var(--muted);
      font-size: 0.88rem;
      font-weight: 700;
    }

    section {
      padding: 76px 0;
    }

    .section-head {
      max-width: 760px;
      margin: 0 auto 42px;
      text-align: center;
    }

    .section-head h3 {
      margin-bottom: 14px;
      color: var(--primary-dark);
      font-size: clamp(2rem, 4vw, 3.1rem);
      line-height: 1.09;
      letter-spacing: -0.045em;
    }

    .section-head p {
      color: var(--muted);
      font-size: 1.02rem;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
    }

    .card {
      padding: 24px;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.78);
      border: 1px solid rgba(77, 114, 35, 0.10);
      box-shadow: 0 14px 34px rgba(45, 75, 18, 0.07);
    }

    .card-icon {
      width: 54px;
      height: 54px;
      display: grid;
      place-items: center;
      margin-bottom: 16px;
      border-radius: 18px;
      background: linear-gradient(135deg, rgba(77, 114, 35, 0.12), rgba(229, 139, 115, 0.18));
      color: var(--primary-dark);
      font-size: 1.4rem;
    }

    .card h4 {
      margin-bottom: 8px;
      color: var(--text);
      font-size: 1.08rem;
    }

    .card p {
      color: var(--muted);
      font-size: 0.95rem;
    }

    .steps-box {
      padding: 42px;
      border-radius: 34px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      box-shadow: var(--shadow);
      overflow: hidden;
      position: relative;
    }

    .steps-box::before {
      content: '';
      position: absolute;
      top: -120px;
      right: -100px;
      width: 300px;
      height: 300px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 50%;
    }

    .steps-box .section-head {
      position: relative;
      z-index: 2;
    }

    .steps-box .section-head h3,
    .steps-box .section-head p {
      color: var(--white);
    }

    .steps {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }

    .step {
      padding: 24px;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .step-number {
      width: 42px;
      height: 42px;
      display: grid;
      place-items: center;
      margin-bottom: 14px;
      border-radius: 50%;
      background: var(--gold);
      color: var(--primary-dark);
      font-weight: 900;
    }

    .step h4 {
      margin-bottom: 8px;
      font-size: 1.06rem;
    }

    .step p {
      color: rgba(255, 255, 255, 0.84);
      font-size: 0.95rem;
    }

    .testimonials {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }

    .testimonial {
      padding: 24px;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.78);
      border: 1px solid rgba(77, 114, 35, 0.10);
      box-shadow: 0 14px 34px rgba(45, 75, 18, 0.07);
    }

    .testimonial h4 {
      margin-bottom: 10px;
      color: var(--primary-dark);
    }

    .testimonial p {
      color: var(--muted);
      font-size: 0.95rem;
    }

    .label {
      display: inline-block;
      margin-top: 14px;
      padding: 7px 12px;
      border-radius: 999px;
      background: rgba(229, 139, 115, 0.14);
      color: #a95c47;
      font-size: 0.82rem;
      font-weight: 800;
    }

    .cta-section {
      padding-top: 20px;
    }

    .cta-box {
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 28px;
      align-items: center;
      padding: 38px;
      border-radius: 34px;
      background: rgba(255, 255, 255, 0.82);
      border: 1px solid rgba(77, 114, 35, 0.10);
      box-shadow: var(--shadow);
    }

    .cta-box h3 {
      margin-bottom: 14px;
      color: var(--primary-dark);
      font-size: clamp(2rem, 4vw, 2.9rem);
      line-height: 1.1;
      letter-spacing: -0.04em;
    }

    .cta-box p {
      margin-bottom: 18px;
      color: var(--muted);
    }

    .checklist {
      display: grid;
      gap: 10px;
      margin-top: 10px;
    }

    .checklist li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: var(--muted);
      list-style: none;
    }

    .checklist li::before {
      content: '✓';
      color: var(--primary);
      font-weight: 900;
    }

    .cta-card {
      padding: 28px;
      border-radius: 26px;
      text-align: center;
      background: linear-gradient(145deg, #f8faea, #fff4ed);
      border: 1px solid rgba(216, 181, 110, 0.25);
    }

    .cta-card .price {
      margin: 10px 0;
      color: var(--primary-dark);
      font-size: 2.25rem;
      font-weight: 900;
      letter-spacing: -0.04em;
    }

    footer {
      margin-top: 70px;
      padding: 36px 0;
      background: var(--primary-dark);
      color: rgba(255, 255, 255, 0.85);
    }

    .footer-inner {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .footer-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .footer-logo {
      width: 52px;
      height: 52px;
      border-radius: 16px;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .footer-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .socials {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .socials a {
      padding: 9px 14px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.20);
      font-size: 0.9rem;
      font-weight: 800;
    }

    .floating-wa {
      position: fixed;
      right: 18px;
      bottom: 18px;
      z-index: 999;
      padding: 14px 18px;
      border-radius: 999px;
      background: #25d366;
      color: var(--white);
      font-weight: 900;
      box-shadow: 0 16px 34px rgba(37, 211, 102, 0.34);
    }



    .registration-form {
      display: grid;
      gap: 16px;
      text-align: left;
      margin-top: 16px;
    }

    .registration-form .form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .registration-form label {
      display: grid;
      gap: 7px;
      color: var(--primary-dark);
      font-size: 0.92rem;
      font-weight: 800;
    }

    .registration-form input,
    .registration-form select,
    .registration-form textarea {
      width: 100%;
      border: 1px solid rgba(77, 114, 35, 0.16);
      border-radius: 16px;
      padding: 13px 14px;
      background: rgba(255, 255, 255, 0.86);
      color: var(--text);
      font: inherit;
      outline: none;
      transition: 0.18s ease;
    }

    .registration-form input:focus,
    .registration-form select:focus,
    .registration-form textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(77, 114, 35, 0.12);
    }

    .registration-form textarea {
      resize: vertical;
    }

    .registration-form .form-note {
      margin: 0;
      color: var(--muted);
      font-size: 0.88rem;
      text-align: center;
    }

    .registration-form .alert {
      padding: 13px 14px;
      border-radius: 16px;
      background: #fff6f2;
      border: 1px solid rgba(198, 111, 90, 0.28);
      color: #9b4c39;
      font-weight: 700;
      text-align: center;
    }

    .program-price-hint {
      color: var(--accent-dark);
      font-weight: 900;
    }

    @media (max-width: 960px) {
      .hero-grid,
      .cta-box {
        grid-template-columns: 1fr;
      }

      .hero-panel {
        min-height: auto;
      }

      .cards {
        grid-template-columns: repeat(2, 1fr);
      }

      .steps,
      .testimonials {
        grid-template-columns: 1fr;
      }

      .hero-points {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 680px) {
      .nav-inner {
        min-height: 78px;
      }

      .nav-links {
        display: none;
      }

      .brand-logo {
        width: 50px;
        height: 50px;
        flex-basis: 50px;
        border-radius: 16px;
      }

      .brand-text h1 {
        font-size: 1rem;
      }

      .brand-text p {
        font-size: 0.73rem;
        white-space: normal;
      }

      .hero {
        padding-top: 52px;
      }

      .hero h2 {
        font-size: clamp(2.35rem, 12vw, 3.6rem);
      }

      .cards {
        grid-template-columns: 1fr;
      }

      .steps-box,
      .cta-box,
      .hero-panel {
        padding: 24px;
        border-radius: 28px;
      }

      .panel-top {
        align-items: flex-start;
      }

      .panel-logo {
        width: 64px;
        height: 64px;
        flex-basis: 64px;
      }

      .btn {
        width: 100%;
      }

      .hero-actions {
        flex-direction: column;
      }
    }


    @media (max-width: 680px) {
      .registration-form .form-grid {
        grid-template-columns: 1fr;
      }
    }

  </style>
</head>
<body>

  <nav class="navbar">
    <div class="container nav-inner">
      <a href="#home" class="brand">
        <div class="brand-logo">
          <img src="logo-ellaifa.png" alt="Logo El-Laifa Qur'an Academy">
        </div>
        <div class="brand-text">
          <h1>El-Laifa Qur'an Academy</h1>
          <p>Living The Quran, Loving The Journey</p>
        </div>
      </a>

      <div class="nav-links">
        <a href="#tentang">Tentang</a>
        <a href="#program">Program</a>
        <a href="#testimoni">Testimoni</a>
        <a href="#daftar" class="btn btn-primary">Daftar Sekarang</a>
      </div>
    </div>
  </nav>

  <main id="home">
    <section class="hero">
      <div class="container hero-grid">
        <div>
          <div class="eyebrow">🌿 Belajar Al-Qur'an dengan nyaman dan terarah</div>
          <h2>Mengaji dari dasar hingga <span>mutqin.</span></h2>
          <p class="hero-desc">
            El-Laifa Qur'an Academy membantu anak, remaja, dan dewasa belajar Al-Qur'an
            secara bertahap: mulai dari tahsin, tajwid praktis, hingga pendampingan rutin
            agar bacaan semakin baik dan hati semakin dekat dengan Al-Qur'an.
          </p>

          <div class="hero-actions">
            <a href="#daftar" class="btn btn-primary">
              Daftar Sekarang
            </a>
            <a href="#program" class="btn btn-secondary">Lihat Program</a>
          </div>

          <div class="hero-points">
            <div class="point">📖 Materi bertahap</div>
            <div class="point">🧑‍🏫 Dibimbing tutor</div>
            <div class="point">💚 Belajar nyaman</div>
          </div>
        </div>

        <div class="hero-panel">
          <div class="panel-top">
            <div class="panel-logo">
              <img src="logo-ellaifa.png" alt="Logo El-Laifa Qur'an Academy">
            </div>
            <div>
              <h3>El-Laifa Qur'an Academy</h3>
              <p>Living The Quran, Loving The Journey</p>
            </div>
          </div>

          <div class="quote-card">
            <div class="arabic">رَبِّ زِدْنِي عِلْمًا</div>
            <strong>“Ya Rabb, tambahkanlah ilmu kepadaku.”</strong>
          </div>

          <div class="program-list">
            <div class="program-mini">
              <strong>Tahsin Dasar</strong>
              <span>Untuk pemula</span>
            </div>
            <div class="program-mini">
              <strong>Tajwid Praktis</strong>
              <span>Langsung praktik</span>
            </div>
            <div class="program-mini">
              <strong>Pendampingan Rutin</strong>
              <span>Sampai terbiasa</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="tentang">
      <div class="container">
        <div class="section-head">
          <h3>Mengapa memilih El-Laifa Qur'an Academy?</h3>
          <p>
            Kami hadir untuk membantu proses belajar Al-Qur'an menjadi lebih mudah,
            menyenangkan, dan sesuai kebutuhan peserta.
          </p>
        </div>

        <div class="cards">
          <div class="card">
            <div class="card-icon">🌱</div>
            <h4>Belajar dari Dasar</h4>
            <p>
              Cocok untuk pemula yang ingin mulai dari pengenalan huruf hijaiyah,
              makhraj, hingga membaca Al-Qur'an dengan baik.
            </p>
          </div>

          <div class="card">
            <div class="card-icon">📚</div>
            <h4>Materi Bertahap</h4>
            <p>
              Materi disusun sistematis agar peserta belajar dengan urutan yang jelas
              dan mudah dipahami.
            </p>
          </div>

          <div class="card">
            <div class="card-icon">🤝</div>
            <h4>Pendampingan Intensif</h4>
            <p>
              Tutor mendampingi peserta dengan pendekatan sabar, ramah, dan sesuai
              kemampuan masing-masing.
            </p>
          </div>

          <div class="card">
            <div class="card-icon">💖</div>
            <h4>Membangun Cinta Qur'an</h4>
            <p>
              Tidak hanya fokus pada bacaan, tetapi juga menumbuhkan semangat dan
              kecintaan dalam perjalanan bersama Al-Qur'an.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section id="program">
      <div class="container">
        <div class="steps-box">
          <div class="section-head">
            <h3>Program belajar kami</h3>
            <p>
              Pilih program yang sesuai dengan kebutuhan belajar Qur'an kamu.
            </p>
          </div>

          <div class="steps">
            <div class="step">
              <div class="step-number">1</div>
              <h4>Tahsin Dasar</h4>
              <p>
                Memperbaiki makhraj, panjang pendek bacaan, dan dasar-dasar tajwid
                agar bacaan semakin benar.
              </p>
            </div>

            <div class="step">
              <div class="step-number">2</div>
              <h4>Tajwid Praktis</h4>
              <p>
                Belajar hukum tajwid dengan contoh yang mudah dipahami dan langsung
                diterapkan dalam bacaan.
              </p>
            </div>

            <div class="step">
              <div class="step-number">3</div>
              <h4>Pendampingan Rutin</h4>
              <p>
                Sesi belajar berkala untuk menjaga semangat, meningkatkan kualitas
                bacaan, dan membentuk kebiasaan baik.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="testimoni">
      <div class="container">
        <div class="section-head">
          <h3>Testimoni peserta</h3>
          <p>
            Beberapa kesan dari peserta yang telah belajar bersama El-Laifa Qur'an Academy.
          </p>
        </div>

        <div class="testimonials">
          <div class="testimonial">
            <h4>Ummu Aisyah</h4>
            <p>
              “Alhamdulillah, belajar di sini membuat saya lebih percaya diri membaca
              Al-Qur'an. Penjelasan tutor sangat mudah dipahami.”
            </p>
            <span class="label">Program Tahsin</span>
          </div>

          <div class="testimonial">
            <h4>Fahri Ramadhan</h4>
            <p>
              “Saya suka suasana belajarnya. Tidak tegang, tapi tetap terarah.
              Tajwid yang dulu bingung jadi lebih paham.”
            </p>
            <span class="label">Program Tajwid</span>
          </div>

          <div class="testimonial">
            <h4>Nabila</h4>
            <p>
              “Yang paling saya rasakan adalah semangat belajar Qur'an jadi meningkat.
              Rasanya lebih dekat dan lebih cinta dengan Al-Qur'an.”
            </p>
            <span class="label">Pendampingan Rutin</span>
          </div>
        </div>
      </div>
    </section>

    <section id="daftar" class="cta-section">
      <div class="container">
        <div class="cta-box">
          <div>
            <h3>Siap memulai perjalanan bersama Al-Qur'an?</h3>
            <p>
              Mari belajar bersama El-Laifa Qur'an Academy dan rasakan pengalaman
              belajar yang lebih terarah, nyaman, dan penuh makna.
            </p>

            <ul class="checklist">
              <li>Program untuk pemula hingga lanjutan</li>
              <li>Pendampingan belajar yang sabar dan nyaman</li>
              <li>Fokus pada kualitas bacaan dan kecintaan pada Al-Qur'an</li>
            </ul>
          </div>

          <div class="cta-card" id="form-pendaftaran">
            <p>Form Pendaftaran Resmi</p>
            <div class="price">Daftar Sekarang</div>
            <p>Isi data berikut. Setelah submit, sistem akan mengarahkan ke pembayaran Midtrans.</p>

            <?php if (!$programs): ?>
              <div class="registration-form">
                <div class="alert">Belum ada program aktif. Admin perlu mengaktifkan program di database.</div>
              </div>
            <?php else: ?>
              <form class="registration-form" action="submit_registration.php" method="post" autocomplete="on">
                <div class="form-grid">
                  <label>Nama Murid
                    <input type="text" name="student_name" required maxlength="150" placeholder="Nama lengkap murid">
                  </label>
                  <label>Nama Orang Tua/Wali
                    <input type="text" name="parent_name" required maxlength="150" placeholder="Nama wali">
                  </label>
                </div>

                <div class="form-grid">
                  <label>Email Orang Tua/Wali
                    <input type="email" name="email" required maxlength="190" placeholder="email@example.com">
                  </label>
                  <label>Nomor WhatsApp
                    <input type="tel" name="phone" required maxlength="30" placeholder="08xxxxxxxxxx">
                  </label>
                </div>

                <div class="form-grid">
                  <label>Program
                    <select name="program_id" required>
                      <option value="">Pilih program</option>
                      <?php foreach ($programs as $program): ?>
                        <option value="<?= h($program['id']) ?>"><?= h($program['name']) ?> - <?= h(rupiah($program['price'])) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </label>
                  <label>Cabang/Area
                    <input type="text" name="branch" maxlength="100" placeholder="Online / Cabang / Area">
                  </label>
                </div>

                <div class="form-grid">
                  <label>Jadwal yang Diminati
                    <input type="text" name="schedule_preference" maxlength="150" placeholder="Contoh: Senin-Rabu sore">
                  </label>
                  <label>Sumber Informasi
                    <input type="text" name="source_info" maxlength="150" placeholder="Instagram / Teman / Iklan / Lainnya">
                  </label>
                </div>

                <label>Catatan Tambahan
                  <textarea name="notes" rows="4" maxlength="1000" placeholder="Kebutuhan khusus, target belajar, dll."></textarea>
                </label>

                <button type="submit" class="btn btn-primary">Daftar & Lanjut Bayar</button>
                <p class="form-note">Data masuk ke database admin, lalu pembayaran diproses lewat Midtrans.</p>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container footer-inner">
      <div class="footer-brand">
        <div class="footer-logo">
          <img src="logo-ellaifa.png" alt="Logo El-Laifa Qur'an Academy">
        </div>
        <div>
          <strong>El-Laifa Qur'an Academy</strong>
          <p>Living The Quran, Loving The Journey</p>
        </div>
      </div>

      <div class="socials">
        <a href="https://www.instagram.com/ellaifaquran/">Instagram</a>
        <a href="https://web.facebook.com/profile.php?id=61588766248051">Facebook</a>
        <a href="https://www.youtube.com/@EllaifaAcademy">YouTube</a>
        <a href="https://www.tiktok.com/@ellaifaquran?_r=1&_t=ZS-96PgAVoAWXa">TikTok</a>
      </div>
    </div>
  </footer>

  <a class="floating-wa" href="#daftar">
    📝 Daftar Murid
  </a>

</body>
</html>
