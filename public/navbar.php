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
                <button class="btn btn-link text-dark dropdown-toggle nav-link" type="button" data-bs-toggle="dropdown" style="text-decoration: none;" id="langToggle">
                    <i class="bi bi-globe"></i> <span id="currentLang">EN</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="langDropdown" style="max-height:350px;overflow-y:auto;min-width:180px;">
                    <li class="px-2 py-1"><input type="text" class="form-control form-control-sm" id="langSearch" placeholder="Search language..." style="font-size:12px;"></li>
                    <li><hr class="dropdown-divider my-1"></li>
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

  var _translations = {};
  var _currentLang = 'en';
  var _base = (typeof _base_url_ !== 'undefined') ? _base_url_ : '/workshop/';

  /* ── All supported languages ───────────────────────────── */
  var LANGUAGES = [
    { code: 'en', name: 'English',    native: 'English',    flag: '🇺🇸' },
    { code: 'zh', name: 'Chinese',    native: '中文',       flag: '🇨🇳' },
    { code: 'es', name: 'Spanish',    native: 'Español',    flag: '🇪🇸' },
    { code: 'fr', name: 'French',     native: 'Français',   flag: '🇫🇷' },
    { code: 'ar', name: 'Arabic',     native: 'العربية',    flag: '🇸🇦' },
    { code: 'de', name: 'German',     native: 'Deutsch',    flag: '🇩🇪' },
    { code: 'pt', name: 'Portuguese', native: 'Português',  flag: '🇧🇷' },
    { code: 'ja', name: 'Japanese',   native: '日本語',     flag: '🇯🇵' },
    { code: 'ko', name: 'Korean',     native: '한국어',     flag: '🇰🇷' },
    { code: 'hi', name: 'Hindi',      native: 'हिन्दी',     flag: '🇮🇳' },
    { code: 'sw', name: 'Swahili',    native: 'Kiswahili',  flag: '🇰🇪' },
    { code: 'ru', name: 'Russian',    native: 'Русский',    flag: '🇷🇺' },
    { code: 'tr', name: 'Turkish',    native: 'Türkçe',     flag: '🇹🇷' },
    { code: 'id', name: 'Indonesian', native: 'Bahasa',     flag: '🇮🇩' },
    { code: 'th', name: 'Thai',       native: 'ไทย',        flag: '🇹🇭' },
    { code: 'vi', name: 'Vietnamese', native: 'Tiếng Việt', flag: '🇻🇳' },
    { code: 'it', name: 'Italian',    native: 'Italiano',   flag: '🇮🇹' },
    { code: 'nl', name: 'Dutch',      native: 'Nederlands', flag: '🇳🇱' },
    { code: 'pl', name: 'Polish',     native: 'Polski',     flag: '🇵🇱' },
    { code: 'sv', name: 'Swedish',    native: 'Svenska',    flag: '🇸🇪' }
  ];

  /* ── Build language dropdown ───────────────────────────── */
  function buildLangDropdown() {
    var dd = document.getElementById('langDropdown');
    if (!dd) return;

    // Keep search bar and divider
    var searchHtml = '<li class="px-2 py-1"><input type="text" class="form-control form-control-sm" id="langSearch" placeholder="Search language..." style="font-size:12px;"></li><li><hr class="dropdown-divider my-1"></li>';

    var itemsHtml = '';
    LANGUAGES.forEach(function (l) {
      var active = l.code === _currentLang ? ' active' : '';
      itemsHtml += '<li><a class="dropdown-item' + active + '" href="#" data-lang="' + l.code + '" onclick="window.changeLanguage(\'' + l.code + '\'); return false;">'
        + '<span class="me-2">' + l.flag + '</span>'
        + '<span class="fw-semibold">' + l.native + '</span>'
        + '<small class="text-muted ms-1">' + l.name + '</small>'
        + '</a></li>';
    });

    dd.innerHTML = searchHtml + itemsHtml;

    // Search filter
    var searchInput = document.getElementById('langSearch');
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        dd.querySelectorAll('li a[data-lang]').forEach(function (a) {
          var li = a.closest('li');
          var text = a.textContent.toLowerCase();
          li.style.display = text.indexOf(q) > -1 ? '' : 'none';
        });
      });
    }
  }

  /* ── Apply translations to DOM ─────────────────────────── */
  function _applyTranslations() {
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      if (!_translations[key]) return;
      if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.setAttribute('placeholder', _translations[key]);
      } else {
        el.textContent = _translations[key];
      }
    });
  }

  /* ── Apply + notify listeners that translations changed ── */
  function _applyAndNotify() {
    _applyTranslations();
    window._i18nLoaded = true;
    document.dispatchEvent(new Event('languageChanged'));
  }

  /* ── Fetch translations from server ────────────────────── */
  function fetchTranslations(lang, callback) {
    // First try the static JSON file (fast, no API needed)
    fetch(_base + 'assets/lang/' + lang + '.json')
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) {
        callback(null, data);
      })
      .catch(function () {
        // Fall back to Translation API endpoint (uses Google Translate if API key configured)
        fetch(_base + 'classes/Translation.php?lang=' + lang)
          .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
          })
          .then(function (data) {
            callback(null, data);
          })
          .catch(function (err2) {
            callback(err2);
          });
      });
  }

  /* ── Main language change function ─────────────────────── */
  function loadLanguage(lang) {
    _currentLang = lang;

    if (lang === 'en') {
      // For English, load the bundled file directly
      fetchTranslations('en', function (err, data) {
        if (err) {
          console.warn('Could not load English translations:', err);
          return;
        }
        _translations = data;
        localStorage.setItem('appLanguage', lang);
        updateUI(lang);
        _applyAndNotify();
      });
      return;
    }

    fetchTranslations(lang, function (err, data) {
      if (err) {
        console.warn('Language file not loaded for ' + lang + ':', err);
        // Try Google Translate API directly if server endpoint also fails
        fetchGoogleTranslateFallback(lang);
        return;
      }
      _translations = data;
      localStorage.setItem('appLanguage', lang);
      updateUI(lang);
      _applyAndNotify();
    });
  }

  /* ── Google Translate fallback via API ──────────────────── */
  function fetchGoogleTranslateFallback(lang) {
    fetch(_base + 'classes/Translation.php?lang=' + lang)
      .then(function (r) { return r.json(); })
      .then(function (data) {
        _translations = data;
        localStorage.setItem('appLanguage', lang);
        updateUI(lang);
        _applyAndNotify();
      })
      .catch(function (err) {
        console.warn('Google Translate fallback failed for ' + lang + ':', err);
        // Last resort: load English
        if (lang !== 'en') {
          loadLanguage('en');
        }
      });
  }

  /* ── Update UI indicators ──────────────────────────────── */
  function updateUI(lang) {
    var badge = document.getElementById('currentLang');
    var info = LANGUAGES.find(function (l) { return l.code === lang; });
    if (badge && info) {
      badge.textContent = info.flag + ' ' + info.code.toUpperCase();
    }
    buildLangDropdown();
  }

  /* ── Expose globally ───────────────────────────────────── */
  window.changeLanguage = loadLanguage;
  window._getTranslation = function (key) { return _translations[key] || ''; };
  window._translations = _translations;
  Object.defineProperty(window, '_translations', {
    get: function () { return _translations; },
    set: function (v) { _translations = v; }
  });

  /* ── Init on page load ─────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    var saved = localStorage.getItem('appLanguage') || 'en';
    _currentLang = saved;
    buildLangDropdown();
    loadLanguage(saved);
  });
})();
</script>

<script>
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
