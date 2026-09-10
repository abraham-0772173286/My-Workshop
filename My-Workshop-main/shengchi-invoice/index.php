<?php
// Shengchi Auto Ltd - Estimate/Invoice Generator (local, no dependencies)
session_start();
if (($_GET['new'] ?? '') === '1') { unset($_SESSION['last_form']); header('Location: index.php'); exit; }
$prev = ($_GET['edit'] ?? '') === '1' && !empty($_SESSION['last_form']) ? $_SESSION['last_form'] : null;
function fv($prev, $k, $d = '') { return $prev !== null && isset($prev[$k]) ? htmlspecialchars($prev[$k]) : $d; }
function fav($prev, $k, $i, $d = '') { return $prev !== null && isset($prev[$k]) && isset($prev[$k][$i]) ? htmlspecialchars($prev[$k][$i]) : $d; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shengchi Auto - New Estimate</title>
<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="bootstrap-icons.min.css">
<style>
  :root {
    --brand-blue: #1d4ed8;
    --brand-blue-dark: #1e3a8a;
    --brand-blue-light: #3b82f6;
    --brand-red: #dc2626;
  }

  body {
    font-family: "Segoe UI", Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #eef2ff 0%, #dbeafe 55%, #bfdbfe 100%);
    min-height: 100vh;
    margin: 0;
    padding: 30px 16px;
    background-attachment: fixed;
  }

  .card-shell {
    max-width: 960px;
    margin: 0 auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 45px rgba(30, 58, 138, .18);
    overflow: hidden;
    border: 1px solid #e2e8f0;
  }

  .hero {
    background: linear-gradient(120deg, var(--brand-blue-dark) 0%, var(--brand-blue) 55%, var(--brand-blue-light) 100%);
    color: #fff;
    padding: 26px 30px;
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .hero .logo-badge {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, .15);
    border: 2px solid rgba(255, 255, 255, .5);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    flex-shrink: 0;
  }

  .hero h1 { font-size: 22px; font-weight: 700; margin: 0; letter-spacing: .3px; }
  .hero p { margin: 2px 0 0; font-size: 13px; opacity: .85; }

  .form-body { padding: 26px 30px 30px; }

  .section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    color: var(--brand-blue-dark);
    margin: 26px 0 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e5e7eb;
    text-transform: uppercase;
    letter-spacing: .5px;
  }
  .section-title:first-of-type { margin-top: 0; }
  .section-title .bi { font-size: 18px; }
  .section-title .badge-line { flex: 1; height: 2px; background: #e5e7eb; display: block; }

  /* Logo uploader */
  .logo-upload {
    display: flex;
    align-items: center;
    gap: 18px;
    background: linear-gradient(135deg, #fafbff 0%, #f1f5ff 100%);
    border: 2px dashed #b6c9f0;
    border-radius: 12px;
    padding: 18px 20px;
  }
  .logo-upload .box {
    width: 120px;
    height: 120px;
    flex-shrink: 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }
  .logo-upload .box img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .logo-upload .box .placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: #94a3b8;
    font-size: 11px;
  }
  .logo-upload .box .placeholder .bi { font-size: 32px; }
  .logo-upload .hint { margin: 8px 0 0; font-size: 12px; color: #64748b; }
  .logo-upload .hint .bi { color: var(--brand-blue); margin-right: 4px; }

  /* Signature uploader */
  .sig-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
  @media (max-width: 820px) { .sig-grid { grid-template-columns: 1fr; } }
  .sig-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 14px;
    background: linear-gradient(180deg, #fafbff 0%, #f5f8ff 100%);
    text-align: center;
  }
  .sig-card .sig-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 12px;
  }
  .sig-card .sig-label .bi { color: var(--brand-blue); margin-right: 4px; }
  .sig-card .sig-box {
    height: 96px;
    background: #fff;
    border: 2px dashed #b6c9f0;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 12px;
  }
  .sig-card .sig-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .sig-card .sig-box .placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    color: #94a3b8;
    font-size: 11px;
  }
  .sig-card .sig-box .placeholder .bi { font-size: 30px; }
  .sig-card .btn { margin: 3px 2px; }

  label.form-label {
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
    letter-spacing: .3px;
  }
  label.form-label .bi { color: var(--brand-blue); margin-right: 4px; }
  .job-note { display: block; font-size: 11px; color: #64748b; margin-top: 3px; }
  body.dark .job-note { color: #94a3b8; }

  .rownum {
    display: inline-block;
    min-width: 26px;
    height: 26px;
    line-height: 26px;
    border-radius: 50%;
    text-align: center;
    background: #eef2ff;
    color: var(--brand-blue);
    font-weight: 700;
    font-size: 12px;
  }
  body.dark .rownum { background: #1b2a42; color: #93c5fd; }
  .table td .rownum { border: 1px solid #c7d5f5; }
  body.dark .table td .rownum { border-color: #33415c; }

  .form-control, .form-select {
    font-size: 13px;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    color: #0f172a;
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  .form-control:focus, .form-select:focus {
    border-color: var(--brand-blue);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, .15);
  }

  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
  .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
  @media (max-width: 640px) { .grid-3, .grid-2 { grid-template-columns: 1fr; } }

  /* ── Tablet (≤ 820px) ── */
  @media (max-width: 820px) {
    body { padding: 14px 8px; }
    .card-shell { border-radius: 12px; }
    .hero { padding: 18px 18px; gap: 12px; }
    .hero h1 { font-size: 17px; }
    .hero p  { font-size: 12px; }
    .form-body { padding: 16px 16px 20px; }
    .section-title { font-size: 13px; }
    .grid-3 { grid-template-columns: repeat(2, 1fr); }
    .sig-grid { grid-template-columns: 1fr 1fr; }
  }

  /* ── Phone (≤ 575px) ── */
  @media (max-width: 575px) {
    body { padding: 8px 4px; }
    .card-shell { border-radius: 8px; }
    .hero { padding: 14px 14px; gap: 10px; }
    .hero .logo-badge { width: 42px; height: 42px; font-size: 20px; }
    .hero h1 { font-size: 15px; }
    .hero p  { font-size: 11px; }
    .form-body { padding: 12px 12px 16px; }
    .section-title { font-size: 12px; margin: 18px 0 10px; }
    .grid-3,
    .grid-2 { grid-template-columns: 1fr; }
    .sig-grid { grid-template-columns: 1fr; }
    .logo-upload { flex-direction: column; align-items: flex-start; gap: 12px; }
    .logo-upload .box { width: 90px; height: 90px; }
    .table { font-size: 12px; }
    .table > :not(caption) > * > * { padding: 6px 5px; }
    .btn { font-size: 12px; padding: 7px 14px; }
    .btn-generate { font-size: 13px; padding: 10px 22px; }
    .actions-bar { justify-content: stretch; }
    .actions-bar .btn-generate { width: 100%; justify-content: center; }
    /* Table: hide less critical columns on tiny screens */
    .table thead th:nth-child(5),
    .table tbody td:nth-child(5) { display: none; } /* VAT col */
  }

  /* ── Touch targets ── */
  @media (hover: none) and (pointer: coarse) {
    .form-control,
    .form-select { min-height: 42px; font-size: 15px !important; }
    .btn { min-height: 40px; }
  }
  .table {
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 8px;
  }
  .table > :not(caption) > * > * {
    border-bottom-width: 1px;
    border-bottom-color: #eef2f7;
    padding: 8px 8px;
    vertical-align: middle;
  }
  .table thead th {
    background: #f1f5f9;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 2px solid #dbe3ee !important;
    white-space: nowrap;
  }
  .table tbody td { background: #fff; }
  .table tbody tr:hover td { background: #f8fafc; }
  .table .form-control {
    border: 1px solid transparent;
    background: transparent;
    padding: 6px 8px;
    font-size: 13px;
    box-shadow: none !important;
  }
  .table .form-control:hover { border-color: #cbd5e1; background: #fff; }
  .table .form-control:focus { border-color: var(--brand-blue); background: #fff; }
  .row-check { width: 16px; height: 16px; cursor: pointer; accent-color: var(--brand-blue); }

  .input-with-unit {
    position: relative;
    display: flex;
    align-items: center;
  }
  .input-with-unit .form-control { width: 100%; padding-right: 36px; }
  .input-with-unit .unit-badge {
    position: absolute;
    right: 8px;
    background: #eef2ff;
    color: var(--brand-blue);
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    pointer-events: none;
  }
  body.dark .input-with-unit .unit-badge { background: #1b2a42; color: #93c5fd; }

  .form-control.is-invalid { border-color: var(--brand-red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,.15) !important; }

  .btn { border-radius: 8px; font-weight: 600; padding: 9px 18px; font-size: 13px; }
  .btn .bi { margin-right: 6px; }

  .btn-danger-soft {
    background: #fef2f2;
    color: var(--brand-red);
    border: 1px solid #fecaca;
    transition: background .15s ease, color .15s ease, box-shadow .15s ease, transform .1s ease;
  }
  .btn-danger-soft:hover {
    background: #dc2626;
    color: #fff;
    border-color: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, .35);
  }
  .btn-danger-soft:focus { box-shadow: 0 0 0 4px rgba(220, 38, 38, .15); }

  .btn-blue {
    background: linear-gradient(120deg, var(--brand-blue) 0%, var(--brand-blue-light) 100%);
    color: #fff;
    border: 1px solid var(--brand-blue);
    transition: background .15s ease, box-shadow .15s ease, transform .1s ease;
  }
  .btn-blue:hover { background: #2563eb; color: #fff; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37, 99, 235, .35); }
  .btn-blue:focus { box-shadow: 0 0 0 4px rgba(59, 130, 246, .25); }

  .btn-outline-blue {
    border: 1px solid var(--brand-blue);
    color: var(--brand-blue);
    background: #fff;
    transition: background .15s ease, color .15s ease, box-shadow .15s ease, transform .1s ease;
  }
  .btn-outline-blue:hover { background: #2563eb; color: #fff; box-shadow: 0 6px 16px rgba(37, 99, 235, .35); }

  .btn-green {
    background: #15803d;
    color: #fff;
    border: 1px solid #14532d;
    box-shadow: 0 4px 12px rgba(21, 128, 61, .3);
    transition: background .15s ease, box-shadow .15s ease, transform .1s ease;
  }
  .btn-green:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(22, 163, 74, .4);
  }
  .btn-green:focus { box-shadow: 0 0 0 4px rgba(21, 128, 61, .25); }

  @keyframes pulseGreen {
    0%   { box-shadow: 0 0 0 0 rgba(21, 128, 61, .55); }
    70%  { box-shadow: 0 0 0 16px rgba(21, 128, 61, 0); }
    100% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0); }
  }
  .btn-pulse {
    animation: pulseGreen 1.6s ease-out infinite;
  }
  .btn-pulse:hover { animation: none; box-shadow: 0 6px 18px rgba(21, 128, 61, .45) !important; }

  .actions-bar {
    display: flex;
    justify-content: flex-end;
    margin-top: 26px;
    padding-top: 20px;
    border-top: 1px solid #e8edf4;
  }
  .btn-generate { padding: 12px 30px; font-size: 15px; }

  .bg-hint {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 4px;
  }
  .bg-hint .bi { color: var(--brand-blue); }

  /* Theme toggle button */
  .theme-toggle {
    margin-left: auto;
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    background: rgba(255, 255, 255, .15);
    border: 2px solid rgba(255, 255, 255, .55);
    border-radius: 50%;
    color: #fff;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .2s ease, transform .2s ease;
  }
  .theme-toggle:hover { background: rgba(255, 255, 255, .32); transform: rotate(20deg) scale(1.08); }

  /* Dark theme */
  body, .card-shell, .form-control, .form-select, .table thead th, .table tbody td, .bg-hint { transition: background-color .25s ease, color .25s ease, border-color .25s ease; }
  body.dark {
    background: linear-gradient(135deg, #0b1220 0%, #0f1b33 55%, #16233f 100%);
    color: #e2e8f0;
  }
  body.dark .card-shell {
    background: #131e30;
    border-color: #2b3b55;
    box-shadow: 0 20px 45px rgba(0, 0, 0, .55);
  }
  body.dark .section-title { color: #dbeafe !important; border-bottom-color: #2b3b55; }
  body.dark .section-title .bi { color: #f8fafc !important; }
  body.dark .section-title .badge-line { background: #2b3b55; }
  body.dark label.form-label { color: #94a3b8; }
  body.dark label.form-label .bi { color: #cbd5e1; }
  body.dark .sig-card .sig-label .bi { color: #cbd5e1; }
  body.dark .bg-hint .bi { color: #e2e8f0; }
  body.dark .logo-upload .hint .bi { color: #e2e8f0; }
  body.dark .form-control, body.dark .form-select {
    color: #e2e8f0;
    background: #0c1322;
    border-color: #33415c;
  }
  body.dark .form-control:hover, body.dark .form-select:hover { border-color: #4a5b7d; }
  body.dark .form-control::placeholder { color: #64748b; }
  body.dark input[type="date"] { color-scheme: dark; }

  body.dark .table { border-color: #2b3b55; }
  body.dark .table > :not(caption) > * > * { border-bottom-color: #22314a; }
  body.dark .table thead th {
    background: #1b2a42;
    color: #a5b4cb;
    border-bottom-color: #2b3b55 !important;
  }
  body.dark .table tbody td { background: #0f1a2c; }
  body.dark .table tbody tr:hover td { background: #15233a; }
  body.dark .table .form-control { background: transparent !important; }
  body.dark .table .form-control:hover { border-color: #33415c; background: #0c1322 !important; }
  body.dark .table .form-control:focus { border-color: var(--brand-blue); background: #0c1322 !important; }
  body.dark .row-check { accent-color: #60a5fa; }

  body.dark .btn-outline-blue { background: #1d2a44; border-color: #3b82f6; color: #93c5fd; }
  body.dark .btn-outline-blue:hover { background: var(--brand-blue); color: #fff; }
  body.dark .btn-danger-soft { background: #3a1d23; border-color: #7f2a35; }
  body.dark .actions-bar { border-top-color: #2b3b55; }
  body.dark .bg-hint { background: #16233a; border-color: #33415c; color: #94a3b8; }

  body.dark .logo-upload {
    background: linear-gradient(135deg, #16233a 0%, #1b2a42 100%);
    border-color: #3b5a8c;
  }
  body.dark .logo-upload .box { background: #0f1a2c; border-color: #2b3b55; }
  body.dark .logo-upload .hint { color: #94a3b8; }

  body.dark .sig-card {
    border-color: #2b3b55;
    background: linear-gradient(180deg, #16233a 0%, #121e31 100%);
  }
  body.dark .sig-card .sig-label { color: #a5b4cb; }
  body.dark .sig-card .sig-box { background: #0f1a2c; border-color: #3b5a8c; }
</style>
</head>
<body>

<div class="card-shell">
  <div class="hero">
    <div class="logo-badge"><i class="bi bi-car-front-fill"></i></div>
    <div>
      <h1>Shengchi Auto Ltd — New Estimate</h1>
      <p>Fill in the details below, then click Generate to get a print-ready document (use your browser's Print → Save as PDF).</p>
    </div>
    <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Toggle dark theme"><i class="bi bi-moon-stars"></i></button>
  </div>

  <div class="form-body">
    <form action="generate.php" method="POST">

      <div class="bg-hint">
        <i class="bi bi-lightbulb-fill"></i>
        Fields marked with a blue label are the ones shown on the document header.
      </div>

      <?php $savedLogo = null;
      foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
        if (is_file(__DIR__ . '/logo/company_logo.' . $ext)) {
          $savedLogo = 'logo/company_logo.' . $ext . '?v=' . @filemtime(__DIR__ . '/logo/company_logo.' . $ext);
          break;
        }
      }
      $savedSigs = [];
      foreach (['customer', 'advisor', 'cashier'] as $slot) {
        $savedSigs[$slot] = null;
        foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
          $f = __DIR__ . '/signatures/' . $slot . '.' . $ext;
          if (is_file($f)) {
            $savedSigs[$slot] = 'signatures/' . $slot . '.' . $ext . '?v=' . @filemtime($f);
            break;
          }
        }
      } ?>

      <div style="margin-bottom: 18px;">
        <a href="index.php?new=1" class="btn btn-outline-blue btn-sm" onclick="return confirm('Start a new estimate? Current unsaved data will be cleared.')"><i class="bi bi-plus-circle-fill"></i>New Estimate</a>
      </div>

      <div class="section-title"><i class="bi bi-image" style="color:#7c3aed;"></i> Company Logo</div>
      <div class="logo-upload">
        <div class="box">
          <?php if ($savedLogo): ?>
            <img id="logoPreview" src="<?php echo $savedLogo; ?>" alt="Company logo">
          <?php else: ?>
            <span class="placeholder" id="logoPlaceholder">
              <i class="bi bi-image"></i> No logo yet
            </span>
            <img id="logoPreview" src="" alt="Company logo" style="display:none;">
          <?php endif; ?>
        </div>
        <div>
          <input type="file" id="logo_file" accept="image/png,image/jpeg,image/gif,image/webp" hidden onchange="uploadLogo(this)">
          <label class="btn btn-green btn-sm" for="logo_file"><i class="bi bi-upload"></i>Upload Logo</label>
          <?php if ($savedLogo): ?>
            <button type="button" class="btn btn-danger-soft btn-sm" onclick="clearLogo()"><i class="bi bi-trash-fill"></i>Remove</button>
          <?php endif; ?>
          <p class="hint"><i class="bi bi-info-circle"></i>Saved automatically. The logo appears on the top of the printed estimate.</p>
        </div>
      </div>

      <?php if ($prev): ?>
      <div class="bg-hint" style="border-color:#4ade80; color:#166534; background:#f0fdf4;">
        <i class="bi bi-pencil-square"></i>
        Editing your previous estimate — all data below was restored. Regenerate to print again.
      </div>
      <?php endif; ?>

      <div class="section-title" style="color:var(--brand-blue);"><i class="bi bi-clipboard2-data" style="color:#0d6efd;"></i> Job / Estimate Info</div>
      <div class="grid-3">
        <div><label class="form-label"><i class="bi bi-hash"></i>RFE No</label><input class="form-control" name="rfe_no" value="<?php echo fv($prev, 'rfe_no', 'N/A'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-card-text"></i>Job Card No</label>
          <?php
          if (!$prev) {
            $counterFile = __DIR__ . '/data/counter.txt';
            $lastUsed = 2197;
            if (is_file($counterFile)) {
              $tmp = (int)trim(file_get_contents($counterFile));
              if ($tmp > 0) $lastUsed = $tmp;
            }
            $autoJobNo = 'HTA-J' . str_pad($lastUsed + 1, 6, '0', STR_PAD_LEFT);
          }
          $jobVal = $prev ? htmlspecialchars($prev['job_card_no'] ?? '') : $autoJobNo;
          ?>
          <input class="form-control" name="job_card_no" value="<?php echo $jobVal; ?>">
          <?php if (!$prev): ?><span class="job-note">Auto-assigned, advances after each print</span><?php endif; ?>
        </div>
        <div><label class="form-label"><i class="bi bi-calendar3"></i>Date</label><input type="date" class="form-control" name="doc_date" value="<?php echo fv($prev, 'doc_date', date('Y-m-d')); ?>"></div>
      </div>
      <div class="grid-3" style="margin-top:14px;">
        <div><label class="form-label"><i class="bi bi-signpost-2"></i>Vehicle No (Plate)</label><input class="form-control" name="vehicle_no" placeholder="UA 388BT" value="<?php echo fv($prev, 'vehicle_no'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-person-badge"></i>Advisor Name</label><input class="form-control" name="advisor_name" value="<?php echo fv($prev, 'advisor_name'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-tools"></i>Service Type</label><input class="form-control" name="service_type" placeholder="Running Repair" value="<?php echo fv($prev, 'service_type'); ?>"></div>
      </div>

      <div class="section-title" style="color:#166534;"><i class="bi bi-person-lines-fill" style="color:#16a34a;"></i> Customer</div>
      <div class="grid-2">
        <div><label class="form-label"><i class="bi bi-person"></i>Customer Name <span style="color:var(--brand-red);">*</span></label><input class="form-control" name="customer_name" id="customerName" value="<?php echo fv($prev, 'customer_name'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-telephone"></i>Mobile No <span style="color:var(--brand-red);">*</span></label><input class="form-control" name="mobile_no" id="mobileNo" value="<?php echo fv($prev, 'mobile_no'); ?>"></div>
      </div>

      <div class="section-title" style="color:#3730a3;"><i class="bi bi-truck-front" style="color:#6366f1;"></i> Vehicle</div>
      <div class="grid-2">
        <div><label class="form-label"><i class="bi bi-car-front"></i>Vehicle (Make/Model/Year)</label><input class="form-control" name="vehicle_desc" placeholder="TOYOTA PRADO AT 2018" value="<?php echo fv($prev, 'vehicle_desc'); ?>" id="vehicleDesc"></div>
        <div><label class="form-label"><i class="bi bi-speedometer"></i>Kilometer</label>
          <div class="input-with-unit">
            <input class="form-control" name="kilometer" id="kilometer" placeholder="50000" value="<?php echo fv($prev, 'kilometer'); ?>">
            <span class="unit-badge">km</span>
          </div>
        </div>
      </div>
      <div class="grid-3" style="margin-top:14px;">
        <div><label class="form-label"><i class="bi bi-palette"></i>Color</label><input class="form-control" name="color" value="<?php echo fv($prev, 'color'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-fuel-pump"></i>Fuel</label>
          <div class="input-with-unit">
            <input class="form-control" name="fuel" id="fuel" placeholder="45" value="<?php echo fv($prev, 'fuel'); ?>">
            <span class="unit-badge">L</span>
          </div>
        </div>
        <div><label class="form-label"><i class="bi bi-envelope"></i>Email</label><input class="form-control" name="cust_email" value="<?php echo fv($prev, 'cust_email'); ?>"></div>
      </div>

      <div class="section-title" style="color:#9a3412;"><i class="bi bi-list-ul" style="color:#ea580c;"></i> Line Items <span class="badge-line"></span></div>
      <table class="table align-middle" id="items">
        <thead>
          <tr>
            <th style="width:3%"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" title="Select all"></th>
            <th style="width:4%">#</th>
            <th>Part / Service</th>
            <th>Description</th>
            <th style="width:7%">VAT %</th>
            <th style="width:7%">Qty</th>
            <th style="width:12%">Unit Price (USH)</th>
            <th style="width:6%"></th>
          </tr>
        </thead>
        <tbody id="itemsBody">
          <?php
          $rowCount = 1;
          if ($prev && isset($prev['part']) && is_array($prev['part'])) {
            $rowCount = max(1, count($prev['part']));
          }
          for ($i = 0; $i < $rowCount; $i++): ?>
          <tr>
            <td class="text-center"><input type="checkbox" class="row-check" onchange="updateSelectAll()"></td>
            <td><span class="rownum fw-bold"><?php echo $i + 1; ?></span></td>
            <td><input class="form-control" name="part[]" list="partsList" placeholder="Select or type part..." value="<?php echo fav($prev, 'part', $i); ?>"></td>
            <td><input class="form-control" name="desc[]" placeholder="Labour" value="<?php echo fav($prev, 'desc', $i); ?>"></td>
            <td><input class="form-control" name="vat[]" value="<?php echo fav($prev, 'vat', $i, '0'); ?>"></td>
            <td><input class="form-control" name="qty[]" value="<?php echo fav($prev, 'qty', $i, '1'); ?>"></td>
            <td><input class="form-control" name="price[]" value="<?php echo fav($prev, 'price', $i, '0'); ?>"></td>
            <td class="text-center"><button type="button" class="btn btn-danger-soft btn-sm" onclick="removeRow(this)" title="Remove line"><i class="bi bi-trash-fill"></i></button></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div style="display:flex; gap:10px; margin-top:8px;">
        <button type="button" class="btn btn-outline-blue btn-sm" onclick="addRow()"><i class="bi bi-plus-circle-fill"></i>Add Item</button>
        <button type="button" class="btn btn-danger-soft btn-sm" onclick="deleteSelected()"><i class="bi bi-trash-fill"></i>Delete Selected</button>
      </div>

      <div class="section-title" style="color:#115e59;"><i class="bi bi-bank" style="color:#0d9488;"></i> Payment Terms (Bank Details)</div>
      <div class="grid-3">
        <div><label class="form-label"><i class="bi bi-buildings"></i>Bank</label><input class="form-control" name="bank" value="<?php echo fv($prev, 'bank', 'Stanbic'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-geo-alt"></i>Branch</label><input class="form-control" name="branch" value="<?php echo fv($prev, 'branch', 'William street'); ?>"></div>
        <div><label class="form-label"><i class="bi bi-123"></i>Account No</label><input class="form-control" name="acc_no" value="<?php echo fv($prev, 'acc_no', '9030021955204'); ?>"></div>
      </div>
      <div class="grid-3" style="margin-top:14px;">
        <div><label class="form-label"><i class="bi bi-person-vcard"></i>Account Name</label><input class="form-control" name="acc_name" placeholder="[ACCOUNT NAME]" value="<?php echo fv($prev, 'acc_name'); ?>"></div>
      </div>

      <div class="section-title" style="color:var(--brand-blue);"><i class="bi bi-pencil-square" style="color:#0d6efd;"></i> Signatures</div>
      <div class="sig-grid">
        <?php $sigSlots = [
          'customer' => ['Customer / Authorized Signatory', 'person-check'],
          'advisor'  => ['Service Advisor Signature', 'wrench-adjustable'],
          'cashier'  => ['Cashier / Authorized Signature', 'cash-coin'],
        ];
        foreach ($sigSlots as $slot => $info): ?>
          <div class="sig-card">
            <span class="sig-label"><i class="bi bi-<?php echo $info[1]; ?>"></i><?php echo $info[0]; ?></span>
            <div class="sig-box">
              <?php if ($savedSigs[$slot]): ?>
                <img src="<?php echo $savedSigs[$slot]; ?>" alt="Signature">
              <?php else: ?>
                <span class="placeholder"><i class="bi bi-pencil"></i>No signature yet</span>
              <?php endif; ?>
            </div>
            <input type="file" id="sig_<?php echo $slot; ?>_file" accept="image/png,image/jpeg,image/gif,image/webp" hidden onchange="uploadSig(this, '<?php echo $slot; ?>')">
            <label class="btn btn-green btn-sm" for="sig_<?php echo $slot; ?>_file"><i class="bi bi-upload"></i>Upload</label>
            <?php if ($savedSigs[$slot]): ?>
              <button type="button" class="btn btn-danger-soft btn-sm" onclick="clearSig('<?php echo $slot; ?>')"><i class="bi bi-trash-fill"></i>Remove</button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="actions-bar">
        <button type="submit" class="btn btn-green btn-generate btn-pulse"><i class="bi bi-file-earmark-arrow-down"></i>Generate Document</button>
      </div>
    </form>
  </div>
</div>

<datalist id="partsList">
  <option value="ENGINE OIL CHANGE">
  <option value="OIL FILTER">
  <option value="AIR FILTER">
  <option value="FUEL FILTER">
  <option value="SPARK PLUGS">
  <option value="BRAKE PADS">
  <option value="BRAKE DISCS">
  <option value="BRAKE FLUID">
  <option value="CLUTCH PLATE">
  <option value="CLUTCH COVER">
  <option value="CLUTCH BEARING">
  <option value="TRANSMISSION OIL">
  <option value="COOLANT / ANTIFREEZE">
  <option value="RADIATOR">
  <option value="WATER PUMP">
  <option value="THERMOSTAT">
  <option value="BELTS (SERPENTINE)">
  <option value="TIMING BELT">
  <option value="TIMING CHAIN">
  <option value="SPARK PLUG WIRE SET">
  <option value="IGNITION COIL">
  <option value="ALTERNATOR">
  <option value="STARTER MOTOR">
  <option value="BATTERY">
  <option value="HEADLIGHT BULB">
  <option value="TAIL LIGHT BULB">
  <option value="HEADLIGHT ADJUSTMENT">
  <option value="WIPER BLADES">
  <option value="WINDSHIELD WASHER FLUID">
  <option value="STEERING RACK">
  <option value="TIE ROD END">
  <option value="BALL JOINT">
  <option value="CONTROL ARM">
  <option value="SHOCK ABSORBER">
  <option value="STRUT MOUNT">
  <option value="SPRING">
  <option value="WHEEL BEARING">
  <option value="HUB ASSEMBLY">
  <option value="CV AXLE / BOOT">
  <option value="DRIVESHAFT">
  <option value="EXHAUST PIPE">
  <option value="MUFFLER / SILENCER">
  <option value="CATALYTIC CONVERTER">
  <option value="FUEL PUMP">
  <option value="INJECTOR CLEANING">
  <option value="THROTTLE BODY CLEANING">
  <option value="PCV VALVE">
  <option value="BLOW-BY VALVE">
  <option value="AC GAS REFILL">
  <option value="AC COMPRESSOR">
  <option value="AC CONDENSER">
  <option value="AC EVAPORATOR">
  <option value="CABIN AIR FILTER">
  <option value="FUSE BOX CHECK">
  <option value="WIRING REPAIR">
  <option value="DOOR LOCK ACTUATOR">
  <option value="WINDOW REGULATOR">
  <option value="MIRROR ASSEMBLY">
  <option value="SEAT BELT">
  <option value="AIRBAG CHECK">
  <option value="LUBRICATION / GREASING">
  <option value="WHEEL ALIGNMENT">
  <option value="WHEEL BALANCING">
  <option value="TYRE ROTATION">
  <option value="TYRE REPLACEMENT">
  <option value="PUNCTURE REPAIR">
  <option value="GEAR OIL CHANGE">
  <option value="DIFFERENTIAL OIL">
  <option value="POWER STEERING FLUID">
  <option value="BRAKE CALIPER REBUILD">
  <option value="WHEEL CYLINDER">
  <option value="HANDBRAKE CABLE">
  <option value="FUEL TANK CLEANING">
  <option value="RADIATOR FLUSH">
  <option value="ENGINE TUNE-UP">
  <option value="VALVE ADJUSTMENT">
  <option value="GASKET REPLACEMENT">
  <option value="HEAD GASKET">
  <option value="PISTON RINGS">
  <option value="BEARINGS (ENGINE)">
  <option value="SEAL REPLACEMENT">
  <option value="OIL PAN GASKET">
  <option value="WINDSHIELD REPLACEMENT">
  <option value="BODY REPAIR">
  <option value="PAINT JOB">
  <option value="POLISHING / BUFFING">
  <option value="RUST TREATMENT">
  <option value="UNDERCOAT">
  <option value="WASH AND VACUUM">
  <option value="DETAILING">
  <option value="LABOUR">
  <option value="DIAGNOSTIC FEE">
  <option value="TOWING FEE">
</datalist>

<script>
function uploadLogo(input) {
  const f = input.files[0];
  if (!f) return;
  const fd = new FormData();
  fd.append('logo_file', f);
  fetch('save_logo.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.ok) {
        const img = document.getElementById('logoPreview');
        const ph = document.getElementById('logoPlaceholder');
        img.src = 'logo/company_logo.' + f.type.split('/')[1].replace('jpeg', 'jpg') + '?v=' + Date.now();
        img.style.display = '';
        if (ph) ph.style.display = 'none';
        location.reload();
      } else {
        alert(d.error || 'Upload failed');
      }
    })
    .catch(() => alert('Upload failed'));
}

function clearLogo() {
  const fd = new FormData();
  fd.append('action', 'clear');
  fetch('save_logo.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.ok) location.reload(); })
    .catch(() => alert('Could not remove logo'));
}

function uploadSig(input, slot) {
  const f = input.files[0];
  if (!f) return;
  const fd = new FormData();
  fd.append('signature_file', f);
  fd.append('slot', slot);
  fetch('save_signature.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.ok) location.reload();
      else alert(d.error || 'Upload failed');
    })
    .catch(() => alert('Upload failed'));
}

function clearSig(slot) {
  const fd = new FormData();
  fd.append('action', 'clear');
  fd.append('slot', slot);
  fetch('save_signature.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.ok) location.reload(); })
    .catch(() => alert('Could not remove signature'));
}

function addRow() {
  const body = document.getElementById('itemsBody');
  const row = body.rows[0].cloneNode(true);
  row.querySelectorAll('input').forEach(i => i.value = i.name === 'vat[]' ? '0' : (i.name === 'qty[]' ? '1' : ''));
  body.appendChild(row);
  renumber();
}
function removeRow(btn) {
  const body = document.getElementById('itemsBody');
  if (body.rows.length > 1) {
    btn.closest('tr').remove();
    renumber();
  }
}
function toggleSelectAll(cb) {
  document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked);
}
function updateSelectAll() {
  const boxes = document.querySelectorAll('.row-check');
  const allChecked = boxes.length > 0 && [...boxes].every(c => c.checked);
  document.getElementById('selectAll').checked = allChecked;
}
function deleteSelected() {
  const body = document.getElementById('itemsBody');
  const selected = document.querySelectorAll('.row-check:checked');
  if (selected.length === 0) { alert('No items selected.'); return; }
  if (selected.length === body.rows.length) { alert('Cannot delete all items.'); return; }
  if (!confirm('Delete ' + selected.length + ' selected item(s)?')) return;
  selected.forEach(c => c.closest('tr').remove());
  renumber();
  document.getElementById('selectAll').checked = false;
}
function renumber() {
  document.querySelectorAll('#itemsBody tr').forEach((r, i) => {
    r.querySelector('.rownum').textContent = i + 1;
  });
}

function toggleTheme() {
  document.body.classList.toggle('dark');
  localStorage.setItem('shengchi-theme', document.body.classList.contains('dark') ? 'dark' : 'light');
  updateThemeIcon();
}
function updateThemeIcon() {
  const icon = document.querySelector('.theme-toggle i');
  if (icon) icon.className = document.body.classList.contains('dark') ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
}
(function () {
  if (localStorage.getItem('shengchi-theme') === 'dark') document.body.classList.add('dark');
  updateThemeIcon();
})();

function clearInvalid() {
  document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}
document.querySelector('form').addEventListener('submit', function(e) {
  clearInvalid();
  const checks = [
    { id: 'customerName', label: 'Customer Name' },
    { id: 'mobileNo', label: 'Mobile No' },
    { id: 'vehicleDesc', label: 'Vehicle (Make/Model/Year)' },
  ];
  let firstEmpty = null;
  checks.forEach(c => {
    const el = document.getElementById(c.id);
    if (!el.value.trim()) {
      el.classList.add('is-invalid');
      if (!firstEmpty) firstEmpty = el;
    }
  });
  if (firstEmpty) {
    e.preventDefault();
    firstEmpty.focus();
  }
});
document.querySelectorAll('.is-invalid').forEach(el => el.addEventListener('input', function() { this.classList.remove('is-invalid'); }));
</script>
</body>
</html>