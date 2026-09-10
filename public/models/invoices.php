<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'invoices';

// Resolve the shengchi-invoice URL relative to the server root
// The folder lives at: /shengchi/My-Workshop-main/shengchi-invoice/
$invoiceAppUrl = '/shengchi/My-Workshop-main/shengchi-invoice/index.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Invoices / Estimates – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script>var _base_url_ = <?= json_encode($workshopBase) ?>;</script>

  <style>
    /* ── Fixed footer for this page only ── */
    .app-footer {
      position: fixed !important;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }
    /* Push main content up so it doesn't hide under the fixed footer */
    .app-main {
      padding-bottom: 58px;
    }

    /* ── iframe wrapper ── */
    .invoice-frame-wrap {
      position: relative;
      width: 100%;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,.10);
      background: #eef2ff;
    }

    #invoiceFrame {
      width: 100%;
      /* starts at a sensible minimum; JS expands it to full content height */
      height: 600px;
      border: none;
      display: block;
    }

    /* ── Fullscreen toggle button ── */
    .frame-controls {
      display: flex;
      gap: 8px;
      align-items: center;
    }
    .frame-controls .btn-fullscreen {
      background: #1d1d4e;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 7px 14px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: background .15s;
    }
    .frame-controls .btn-fullscreen:hover { background: #4015bf; }

    /* ── Fullscreen overlay mode ── */
    .invoice-frame-wrap.fullscreen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
      border-radius: 0;
      min-height: unset;
      overflow: hidden;
    }
    /* In fullscreen the iframe must fill the whole overlay */
    .invoice-frame-wrap.fullscreen #invoiceFrame {
      height: 100vh !important;
      overflow-y: auto;
    }
    /* Hide the page footer when iframe is fullscreen */
    .invoice-frame-wrap.fullscreen ~ * .app-footer,
    body.inv-fullscreen .app-footer {
      display: none !important;
    }
    .invoice-frame-wrap.fullscreen .exit-fs-btn { display: flex !important; }

    /* ── Exit fullscreen button (shown only in fullscreen) ── */
    .exit-fs-btn {
      display: none;
      position: absolute;
      top: 12px;
      right: 14px;
      z-index: 10000;
      background: rgba(220,38,38,.9);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 7px 14px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      align-items: center;
      gap: 6px;
    }
    .exit-fs-btn:hover { background: #dc2626; }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <?php include '../navbar.php'; ?>
  <?php include '../sidebar.php'; ?>

  <main class="app-main">
    <div class="app-content-header px-4 pt-3 pb-0">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold mb-0">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>Invoices &amp; Estimates
          </h4>
          <p class="text-muted small mb-0">Generate branded print-ready estimates and invoices</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
            <li class="breadcrumb-item"><a href="../index.php" class="text-primary">Home</a></li>
            <li class="breadcrumb-item active">Invoices</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="app-content p-4">

      <!-- Toolbar -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2" style="border-radius:8px;">
            <i class="bi bi-info-circle me-1"></i>
            Fill the form and click <strong>Generate Document</strong> to get a print-ready estimate
          </span>
        </div>
        <div class="frame-controls">
          <button class="btn-fullscreen" onclick="toggleFullscreen()">
            <i class="bi bi-fullscreen" id="fsIcon"></i> Fullscreen
          </button>
          <a href="<?= htmlspecialchars($invoiceAppUrl) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-1"></i>Open in new tab
          </a>
        </div>
      </div>

      <!-- iframe wrapper -->
      <div class="invoice-frame-wrap" id="frameWrap">
        <button class="exit-fs-btn" id="exitFsBtn" onclick="toggleFullscreen()">
          <i class="bi bi-fullscreen-exit"></i> Exit Fullscreen
        </button>
        <iframe
          id="invoiceFrame"
          src="<?= htmlspecialchars($invoiceAppUrl) ?>"
          title="Shengchi Invoice / Estimate Generator"
          allowfullscreen
        ></iframe>
      </div>

    </div>
  </main>

  <footer class="app-footer">
    <div class="footer-content">
      <div class="text-muted small order-2 order-md-1">
        <strong>Copyright &copy; 2026</strong> <span class="d-none d-sm-inline">| All Rights Reserved.</span>
      </div>
      <div class="order-1 order-md-2 text-center">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#adb5bd;font-weight:600;" class="mb-1">Think of it, We Develop it.</div>
        <a href="https://pearl-host.com/" target="_blank" class="text-decoration-none text-primary text-uppercase fw-bold">
          <i class="bi bi-gem me-1"></i> AB Solutions
        </a>
      </div>
      <div class="footer-contacts order-3">
        <a href="https://wa.me/256772173286" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        <a href="tel:+256763808854" title="Call"><i class="bi bi-telephone-outbound"></i></a>
        <a href="mailto:support@pearl-host.com" title="Email"><i class="bi bi-envelope-at"></i></a>
      </div>
    </div>
  </footer>

</div><!-- /.app-wrapper -->

<script>
  function toggleFullscreen() {
    const wrap = document.getElementById('frameWrap');
    const icon = document.getElementById('fsIcon');
    const isFs = wrap.classList.toggle('fullscreen');
    icon.className = isFs ? 'bi bi-fullscreen-exit' : 'bi bi-fullscreen';
    document.body.classList.toggle('inv-fullscreen', isFs);
    document.body.style.overflow = isFs ? 'hidden' : '';
    // In fullscreen the iframe fills 100vh; in normal mode expand to content height
    if (!isFs) resizeFrame();
  }

  // Allow ESC to exit fullscreen
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const wrap = document.getElementById('frameWrap');
      if (wrap.classList.contains('fullscreen')) toggleFullscreen();
    }
  });

  const frame = document.getElementById('invoiceFrame');

  function resizeFrame() {
    // Only resize when NOT in fullscreen mode
    if (document.getElementById('frameWrap').classList.contains('fullscreen')) return;
    try {
      const doc = frame.contentDocument || frame.contentWindow.document;
      // Use scrollHeight of the full document body so all content is reachable
      const h = Math.max(600, doc.documentElement.scrollHeight, doc.body.scrollHeight);
      frame.style.height = h + 'px';
    } catch(e) {
      // cross-origin fallback — just make it very tall
      frame.style.height = '2400px';
    }
  }

  frame.addEventListener('load', function() {
    resizeFrame();

    // After resize settles, scroll the outer page back to the very top
    // Use a small delay so the layout has fully painted before we scroll
    setTimeout(function() {
      window.scrollTo({ top: 0, behavior: 'instant' });
    }, 50);

    // Watch for DOM mutations inside the iframe (e.g. adding/removing line item rows)
    // so the height updates automatically without needing a full page reload
    try {
      const observer = new MutationObserver(function() {
        clearTimeout(frame._resizeTimer);
        frame._resizeTimer = setTimeout(resizeFrame, 80);
      });
      observer.observe(
        frame.contentDocument.body,
        { childList: true, subtree: true, attributes: false }
      );
    } catch(e) { /* cross-origin, mutations not observable */ }
  });
</script>
</body>
</html>
