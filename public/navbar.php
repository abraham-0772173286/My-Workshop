<nav class="app-header navbar navbar-expand bg-body mb-0 sticky-top">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" id="sidebarToggle" href="#" role="button" aria-label="Toggle navigation" aria-expanded="true">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav gap-2 ms-auto align-items-center">

            <li class="nav-item dropdown">
                <a class="nav-link position-relative" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell fs-5"></i>
                    <span id="notif-badge" class="position-absolute badge rounded-pill bg-danger"
                        style="display:none; font-size: 0.65rem; padding: 2px 5px; top: 5px; right: 5px; border: 2px solid #fff;">
                        0 </span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">

                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span data-i18n="notifications">Notifications</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">0 New</span>
                    </div>
                    <div class="dropdown-divider m-0"></div>

                    <div class="notif-scroll" style="max-height: 300px; overflow-y: auto;">
                        <div class="p-4 text-center">
                            <i class="bi bi-check2-circle text-success display-6"></i>
                            <p class="text-muted small mt-2" data-i18n="noUnreadNotifications">No unread notifications</p>
                        </div>
                    </div>

                    <div class="dropdown-divider m-0"></div>
                    <a href="#" class="dropdown-item dropdown-footer" data-i18n="viewAllNotifications">View All Notifications</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <button class="btn btn-link text-dark dropdown-toggle nav-link" type="button" data-bs-toggle="dropdown" style="text-decoration: none;">
                    <i class="bi bi-globe"></i> <span id="currentLang">EN</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('en'); return false;">🇺🇸 English</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('es'); return false;">🇪🇸 Español</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('fr'); return false;">🇫🇷 Français</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changeLanguage('zh'); return false;">🇨🇳 中文</a></li>
                </ul>
            </li>

            <li class="nav-item d-none d-md-block">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                </a>
            </li>

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle text-dark fs-5"></i>
                    <span class="d-none d-md-inline text-dark fw-medium small ms-1"><?= htmlspecialchars((string) ($_SESSION['user']['name'] ?? 'System'), ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-sm">
                    <li class="user-header bg-light text-center p-4">
                        <i class="bi bi-person-circle text-primary" style="font-size: 80px;"></i>
                        <p class="mb-0 fw-bold text-dark"><?= htmlspecialchars((string) ($_SESSION['user']['name'] ?? 'Support System'), ENT_QUOTES, 'UTF-8') ?></p>
                        <small class="text-muted"><?= htmlspecialchars((string) ($_SESSION['user']['role'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></small>
                    </li>
                    <li class="user-footer d-flex justify-content-between p-3 bg-white">
                        <a href="#" class="btn btn-outline-primary btn-sm px-3" data-i18n="profile">Profile</a>
                        <a href="../classes/Login.php?f=logout" class="btn btn-outline-danger btn-sm px-3" data-i18n="signOutNav">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script>
(function () {
  'use strict';

  let _translations = {};

  function _applyTranslations() {
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      const key = el.getAttribute('data-i18n');
      if (!_translations[key]) return;
      if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.setAttribute('placeholder', _translations[key]);
      } else {
        el.textContent = _translations[key];
      }
    });
  }

  function loadLanguage(lang) {
    // _base_url_ is set per-page before navbar.php is included
    const base = (typeof _base_url_ !== 'undefined') ? _base_url_ : '/workshop/';
    fetch(base + 'assets/lang/' + lang + '.json')
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) {
        _translations = data;
        localStorage.setItem('appLanguage', lang);
        const badge = document.getElementById('currentLang');
        if (badge) badge.textContent = lang.toUpperCase();
        _applyTranslations();
      })
      .catch(function (err) {
        console.warn('Language file not loaded:', err);
      });
  }

  // Expose globally so onclick handlers in the dropdown work
  window.changeLanguage = loadLanguage;

  // Auto-load saved language on every page
  document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('appLanguage') || 'en';
    loadLanguage(saved);
  });
})();

(function () {
  'use strict';

  var toggle = document.querySelector('[data-lte-toggle="fullscreen"]');
  if (!toggle) return;

  var icon = toggle.querySelector('i');
  var enterIcon = 'bi-arrows-fullscreen';
  var exitIcon = 'bi-arrows-fullscreen-exit';

  function setIcon(isFullscreen) {
    if (icon) {
      icon.classList.toggle(enterIcon, !isFullscreen);
      icon.classList.toggle(exitIcon, isFullscreen);
    }
    toggle.setAttribute('aria-expanded', isFullscreen ? 'true' : 'false');
    toggle.setAttribute('title', isFullscreen ? 'Exit full screen' : 'Enter full screen');
  }

  toggle.addEventListener('click', function (e) {
    e.preventDefault();
    if (document.fullscreenElement) {
      document.exitFullscreen();
    } else {
      document.documentElement.requestFullscreen();
    }
  });

  document.addEventListener('fullscreenchange', function () {
    setIcon(!!document.fullscreenElement);
  });
})();
</script>
