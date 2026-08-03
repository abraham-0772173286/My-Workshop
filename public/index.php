<!doctype html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>JIN LONG GARAGE - Workshop Management</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    .toast-success {
      background-color: #28a745 !important;
      color: white !important;
    }

    .toast-error {
      background-color: #dc3545 !important;
      color: white !important;
    }

    .toast-warning {
      background-color: rgb(229, 156, 54);
      color: black;
    }

    .modal-fullscreen {
      width: 100vw !important;
      height: 100vh !important;
      margin: 0 !important;
      top: 0 !important;
      left: 0 !important;
    }
  </style>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap');

    * {
      font-family: 'DM Sans', sans-serif;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- TomSelect CSS and JS -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">


  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Bootstrap 5.3 JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Toastr CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />

  <!-- Bootstrap CSS and JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- Font Awesome (Free) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

  <!-- Define base URL for JavaScript usage -->
  <script>
    var _base_url_ = window.location.origin + '/workshop/';

    const senderName = "Support System";
    const currentUserId = "lakGilwPc4nZUm1L5wau";
  </script>

  </script>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">

  <!-- Custom CSS -->
  <!-- Removed external CSS links -->

  <!-- Custom JS -->
  <!-- Removed external JS link -->

  <!-- Third Party Plugin(OverlayScrollbars)-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
    integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />

  <!-- Third Party Plugin(Bootstrap Icons)-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />

  <!-- Required Plugin(AdminLTE)-->
  <!-- AdminLTE CSS removed - using Bootstrap 5 directly -->

  <!-- DataTables Buttons CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

  <!-- DataTables Buttons JS -->
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

  <!-- JSZip for Excel export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

  <!-- pdfmake for PDF export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

  <!-- Buttons for HTML5 export and print -->
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script>
    // Stub functions for loader
    function start_loader() { }
    function end_loader() { }
    function playNotificationSound(a, b, c, d) { }
    function pushNotification(title, msg, type, icon) {
      toastr[type] ? toastr[type](msg, title) : toastr.info(msg, title);
    }
  </script>
</head>
<div id="notification-stack"></div>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

  <div class="app-wrapper">

    <style>
      /* Clean Navbar Styling */
      .app-header {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0;
      }

      /* Notification Badge */
      .navbar-badge {
        font-size: 0.65rem;
        padding: 2px 5px;
        top: 5px !important;
        right: 5px !important;
        border: 2px solid #fff;
        /* Makes the badge "pop" against the icon */
      }

      /* Dropdown Enhancements */
      .dropdown-menu-lg {
        min-width: 320px;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        margin-top: 10px;
      }

      .dropdown-header {
        font-weight: 700;
        color: #1e293b;
        padding: 1rem;
      }

      .dropdown-item {
        padding: 0.75rem 1rem;
        transition: all 0.2s;
        border-bottom: 1px solid #f8fafc;
      }

      .dropdown-item:hover {
        background-color: #f1f5f9;
      }

      .dropdown-footer {
        text-align: center;
        font-weight: 600;
        color: #0d6efd;
        padding: 0.75rem;
      }

      /* User Avatar Styling */
      .user-image {
        width: 35px;
        height: 35px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
      }
    </style>
    <?php include 'navbar.php'; ?>

    <style>
      .garage-sidebar {
        position: fixed;
        z-index: 1045;
        top: 0;
        left: 0;
        width: 260px;
        height: 100vh;
        scrollbar-width: none;
        -ms-overflow-style: none;
        overflow-y: auto;
        background: linear-gradient(180deg, #4015bf 0%, #29106f 100%) !important;
        transition: transform .28s ease;
        box-shadow: 5px 0 24px rgba(21, 5, 64, .16);
      }

      .garage-sidebar::-webkit-scrollbar {
        display: none;
      }

      .garage-sidebar.is-collapsed {
        transform: translateX(-100%);
      }

      .app-header {
        margin-left: 260px;
        transition: margin-left .28s ease;
      }

      .app-main {
        margin-left: 260px !important;
        transition: margin-left .28s ease;
      }

      body.garage-sidebar-collapsed .app-header,
      body.garage-sidebar-collapsed .app-main {
        margin-left: 0 !important;
      }

      .garage-sidebar .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 19px 18px;
        color: #fff;
        text-decoration: none;
      }

      .garage-sidebar .brand-mark {
        width: 39px;
        height: 39px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: #f5ad2b;
        color: #301078;
        font-size: 20px;
      }

      .garage-sidebar .brand-name {
        font-size: 15px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: .3px;
      }

      .garage-sidebar .brand-name small {
        display: block;
        margin-top: 4px;
        color: #cfbefd;
        font-size: 9px;
        letter-spacing: 1px;
      }

      .garage-sidebar .nav-sidebar {
        padding: 17px 7px;
      }

      .garage-sidebar .nav-item {
        margin: 3px 0;
      }

      .garage-sidebar .nav-link {
        display: flex;
        align-items: center;
        min-height: 50px;
        gap: 13px;
        padding: 12px 18px;
        color: #fff !important;
        border-radius: 0;
        font-size: 16px;
      }

      .garage-sidebar .nav-link:hover,
      .garage-sidebar .nav-link.active {
        background: rgba(154, 109, 255, .35);
        color: #fff !important;
      }

      .garage-sidebar .nav-link i:first-child {
        width: 29px;
        font-size: 23px;
        text-align: center;
      }

      .garage-sidebar .nav-link .nav-arrow {
        margin-left: auto;
        font-size: 17px;
      }

      .garage-sidebar .nav-header {
        color: #bdaaff !important;
        padding: 20px 18px 7px !important;
      }

      .garage-sidebar .sidebar-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 15px 19px;
        border-top: 1px solid rgba(255, 255, 255, .12);
        color: #d2c7f1;
        font-size: 11px;
      }

      .garage-sidebar .sidebar-footer i {
        color: #f5ad2b;
      }

      @media (max-width: 991px) {

        .app-header,
        .app-main {
          margin-left: 0 !important;
        }

        .garage-sidebar {
          transform: translateX(-100%);
        }

        .garage-sidebar.is-open {
          transform: translateX(0);
        }
      }

      /* 3. Global Text & Icon Color (Force White) */
      .nav-sidebar .nav-link,
      .nav-sidebar .nav-link p,
      .nav-sidebar .nav-link i {
        color: rgba(255, 255, 255, 0.9) !important;
        /* Soft white, never black */
        font-weight: 400;
      }

      .sidebar-brand {
        background: rgba(0, 0, 0, 0.15);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.2rem 0;
      }

      .nav-header {
        font-size: 0.7rem !important;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        color: rgba(10, 59, 219, 0.4) !important;
        /* Faded white for headers */
        padding: 1.8rem 1.5rem 0.5rem !important;
      }

      .nav-arrow {
        font-size: 0.8rem;
      }

      /* Logout Button Special Styling */
      .nav-link.logout-btn:hover {
        background: #5022f6 !important;
      }
    </style>

    <aside class="garage-sidebar" id="garageSidebar" aria-label="Main navigation">
      <a href="#" class="sidebar-brand">
        <span class="brand-mark"><i class="bi bi-wrench-adjustable"></i></span>
        <span class="brand-name" >JIN LONG GARAGE<small>金龙汽车维修</small></span>
      </a>
      <nav class="nav-sidebar">
        <div class="nav-header" data-i18n="workspace">WORKSPACE</div>
        <div class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i><span data-i18n="dashboard">Dashboard</span></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-clipboard2-check"></i><span data-i18n="repairJobs">Repair Jobs</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-people"></i><span data-i18n="customers">Customers</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-car-front"></i><span data-i18n="vehicles">Vehicles</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-cash-stack"></i><span data-i18n="payments">Payments</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-receipt"></i><span data-i18n="receipts">Receipts</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-bar-chart-line"></i><span data-i18n="reports">Reports</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-gear"></i><span data-i18n="settings">Settings</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="nav-item"><a href="#" class="nav-link"><i class="bi bi-receipt"></i><span data-i18n="signOut">Sign Out</span><i class="bi bi-chevron-right nav-arrow"></i></a></div>
        <div class="sidebar-footer"><i class="bi bi-shield-check me-1"></i> <span data-i18n="garageOps">Garage operations system</span></div>
      </nav>
         
    </aside>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('garageSidebar');
        if (!toggle || !sidebar) return;
        toggle.addEventListener('click', function(event) {
          event.preventDefault();
          const compact = window.matchMedia('(max-width: 991px)').matches;
          sidebar.classList.toggle(compact ? 'is-open' : 'is-collapsed');
          document.body.classList.toggle('garage-sidebar-collapsed', !compact && sidebar.classList.contains('is-collapsed'));
          toggle.setAttribute('aria-expanded', compact ? sidebar.classList.contains('is-open') : !sidebar.classList.contains('is-collapsed'));
        });
      });
    </script>

    <script>
      var page = 'users';
      if ($('.nav-' + page).length > 0) {
        $('.nav-' + page).addClass('active');
        $('.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
        var mainNavText = $.trim($('.nav-' + page).closest('.nav-treeview').closest('li').children('a').text());

      }
    </script>
    <main class="app-main">

      <div class="app-content-header">

        <div class="row mt-0 mb-0">
          <div class="col-sm-6 dashboard-title mb-0 mt-0">
            <h3 class="mb-0"></h3>
          </div>
          <div class="col-sm-6 mt-0">
            <ol class="breadcrumb float-sm-end" style="--bs-breadcrumb-divider: '›';">
              <li class="breadcrumb-item text-primary"><a href="#" id="MainNavText" data-i18n="home">Home</a></li>
              <li class="breadcrumb-item active text-capitalize" aria-current="page">
                <span data-i18n="users">users</span></li>
            </ol>

          </div>
        </div>

      </div>

      <div class="app-content p-0 m-0">

        <style>
          /* Repair job status */
          .status-pill {
            padding: 0.35em 0.8em;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 50px;
            letter-spacing: 0.025em;
          }

          .status-done {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
          }

          .status-pending {
            background: #fff4d8;
            color: #9a5a00;
            border: 1px solid #ffe3a2;
          }

          /* User Identity Styling */
          .user-avatar-rect {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
          }

          .user-type-badge {
            font-size: 0.65rem;
            font-weight: 600;
            color: #6366f1;
            background: #eef2ff;
            padding: 1px 6px;
            border-radius: 4px;
            margin-top: 3px;
            display: inline-block;
          }

          /* Toolbar Organization */
          .action-group {
            border-right: 1px solid #e2e8f0;
            padding-right: 1rem;
            margin-right: 1rem;
          }

          /* Custom Checkbox Styling */
          .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
          }
        </style>

        <div class="card customized-card shadow-sm border-0">
          <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="card-title fw-bold mb-0" data-i18n="repairJobsRegister">Repair Jobs Register</h3>
              </div>
              <button type="button" id="registerRepairJob" class="btn btn-primary btn-flat btn-sm shadow-sm">
                <i class="fa fa-plus me-2"></i><span data-i18n="registerRepairJob">Register Repair Job</span>
              </button>
            </div>
          </div>

          <div class="card-body p-4">
            <div id="toolbar" class="mb-4 pb-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="action-group d-flex gap-2">
                  <button class="btn btn-outline-success btn-flat btn-sm edit" title="Edit Profile">
                    <i class="fa fa-edit me-1"></i> <span data-i18n="edit">Edit</span>
                  </button>
                  <button class="btn btn-outline-warning btn-flat btn-sm reset" title="Reset Credentials">
                    <i class="fa fa-key me-1"></i> <span data-i18n="password">Password</span>
                  </button>
                </div>

                <div class="action-group d-flex gap-2">
                  <button class="btn btn-outline-info btn-flat btn-sm unlockuser">
                    <i class="fa fa-unlock me-1"></i> <span data-i18n="unlock">Unlock</span>
                  </button>
                  <button class="btn btn-outline-danger btn-flat btn-sm lockuser">
                    <i class="fa fa-lock me-1"></i> <span data-i18n="lock">Lock</span>
                  </button>
                </div>

                <div class="d-flex gap-2">
                  <button class="btn btn-outline-dark btn-flat btn-sm assignrole">
                    <i class="bi bi-shield-lock me-1"></i> <span data-i18n="roles">Roles</span>
                  </button>
                  <button class="btn btn-outline-secondary btn-flat btn-sm assignreports">
                    <i class="bi bi-file-earmark-text me-1"></i> <span data-i18n="assignReports">Reports</span>
                  </button>
                  <button class="btn btn-outline-danger btn-flat btn-sm deleteuser ms-2">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </div>

              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-light border" onclick="exportTable('print', table)">
                  <i class="fa fa-print"></i>
                </button>
                <div class="dropdown">
                  <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> <span data-i18n="export">Export</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item small" href="#" onclick="exportTable('pdf', table)"><i class="fa fa-file-pdf text-danger me-2"></i><span data-i18n="pdfDocument">PDF Document</span></a></li>
                    <li><a class="dropdown-item small" href="#" onclick="exportTable('excel')"><i class="fa fa-file-excel text-success me-2"></i><span data-i18n="excelSpreadsheet">Excel Spreadsheet</span></a></li>
                    <li><a class="dropdown-item small" href="#" onclick="exportTable('csv', table)"><i class="fa fa-file-csv text-info me-2"></i><span data-i18n="csvFile">CSV File</span></a></li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="table-responsive customized-table-container">
              <table id="userstable" class="table customized-table align-middle w-100">
                <thead>
                  <tr>
                    <th style="width: 30px;"></th>
                    <th data-i18n="jobNo">Job No.</th>
                    <th data-i18n="customer">Customer</th>
                    <th data-i18n="vehicle">Vehicle</th>
                    <th data-i18n="repairType">Repair Type</th>
                    <th data-i18n="partsCost">Parts Cost</th>
                    <th data-i18n="labourCost">Labour Cost</th>
                    <th data-i18n="status">Status</th>
                    <th data-i18n="date">Date</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <div class="modal fade" id="repairJobModal" tabindex="-1" aria-labelledby="repairJobModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-3">
              <div class="modal-header bg-primary text-white">
                <div>
                  <h5 class="modal-title fw-bold" id="repairJobModalLabel" data-i18n="registerRepairJob">Register Repair Job</h5><small class="opacity-75" data-i18n="registerJob">Customer, vehicle and repair details</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="repairJobForm">
                <div class="modal-body p-4">
                  <div class="row g-3">
                    <div class="col-12">
                      <h6 class="text-uppercase text-muted small fw-bold mb-0" data-i18n="customerDetails">Customer details</h6>
                    </div>
                    <div class="col-md-6"><label class="form-label small fw-semibold" data-i18n="customerName">Customer name <span class="text-danger">*</span></label><input class="form-control" name="customer_name" required></div>
                    <div class="col-md-6"><label class="form-label small fw-semibold" data-i18n="phoneContact">Phone / contact <span class="text-danger">*</span></label><input class="form-control" name="contact" required></div>
                    <div class="col-12"><label class="form-label small fw-semibold" data-i18n="address">Address</label><input class="form-control" name="address"></div>
                    <div class="col-12 pt-2">
                      <h6 class="text-uppercase text-muted small fw-bold mb-0" data-i18n="vehicleRepair">Vehicle and repair</h6>
                    </div>
                    <div class="col-md-6"><label class="form-label small fw-semibold" data-i18n="plateNumber">Plate number <span class="text-danger">*</span></label><input class="form-control text-uppercase" name="plate_number" placeholder="KDD 821T" required></div>
                    <div class="col-md-6"><label class="form-label small fw-semibold" data-i18n="vehicleModel">Vehicle make / model</label><input class="form-control" name="model" placeholder="Toyota Prado"></div>
                    <div class="col-12"><label class="form-label small fw-semibold" data-i18n="repairTypeLabel">Repair type <span class="text-danger">*</span></label><input class="form-control" name="repair_type" placeholder="e.g. Brake pads replacement" required></div>
                    <div class="col-md-4"><label class="form-label small fw-semibold" data-i18n="partsCostLabel">Parts cost (KES) <span class="text-danger">*</span></label><input class="form-control" type="number" name="parts_cost" min="0" step="0.01" value="0" required></div>
                    <div class="col-md-4"><label class="form-label small fw-semibold" data-i18n="labourCostLabel">Labour cost (KES) <span class="text-danger">*</span></label><input class="form-control" type="number" name="labour_cost" min="0" step="0.01" value="0" required></div>
                    <div class="col-md-4"><label class="form-label small fw-semibold" data-i18n="repairStatus">Repair status <span class="text-danger">*</span></label><select class="form-select" name="status" required>
                        <option value="REPAIR PENDING" data-i18n="repairPending" required>REPAIR PENDING</option>
                        <option value="REPAIR DONE" data-i18n="repairDone" required>REPAIR DONE</option>
                      </select></div>
                  </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button><button type="submit" class="btn btn-primary" id="saveRepairJob"><i class="bi bi-save me-1"></i><span data-i18n="saveRepairJob">Save Repair Job</span></button></div>
              </form>
            </div>
          </div>
        </div>
        <script>
          let table = null;
          let selecteduser = null;

          $(document).ready(function() {
            // Don't load table yet - wait for translations to load first
          });

          const loadDataLabel = () => {
            table = $("#userstable").DataTable({
              responsive: true,
              processing: true,
              pageLength: 50,
              dom: 'lfrtip',
              buttons: [{
                  extend: 'copy',
                  text: '<i class="fa fa-copy"></i> Copy',
                  className: 'btn btn-secondary btn-flat btn-sm',
                  exportOptions: {
                    columns: ':not(:last-child)'
                  }
                },
                {
                  extend: 'csv',
                  text: '<i class="fa fa-file-csv"></i> CSV',
                  className: 'btn btn-success btn-flat btn-sm',
                  exportOptions: {
                    columns: ':not(:last-child)'
                  }
                },
                {
                  extend: 'excel',
                  text: '<i class="fa fa-file-excel"></i> Excel',
                  className: 'btn btn-success btn-flat btn-sm',
                  exportOptions: {
                    columns: ':not(:last-child)'
                  }
                },
                {
                  extend: 'pdf',
                  text: '<i class="fa fa-file-pdf"></i> PDF',
                  className: 'btn btn-danger btn-flat btn-sm',
                  exportOptions: {
                    columns: ':not(:last-child)'
                  }
                },
                {
                  extend: 'print',
                  text: '<i class="fa fa-print"></i> Print',
                  className: 'btn btn-info',
                  exportOptions: {
                    columns: ':not(:last-child)'
                  }
                }
              ],
              ajax: {
                url: '../classes/RepairJobs.php?f=viewall',
                dataSrc: ''
              },
              columns: [{
                  data: "repair_job_id",
                  orderable: false,
                  render: (d) => `<div class="form-check"><input class="form-check-input" type="checkbox" value="${d}"></div>`
                },
                {
                  data: "job_no",
                  render: d => `<code class="text-primary fw-bold">${d}</code>`
                },
                {
                  data: "customer",
                  render: d => `<span class="fw-bold text-dark">${d}</span>`
                },
                {
                  data: "vehicle",
                  render: d => `<span class="small"><i class="bi bi-car-front me-1 text-muted"></i>${d}</span>`
                },
                {
                  data: "repair_type"
                },
                {
                  data: "parts_cost",
                  render: d => `KES ${Number(d).toLocaleString()}`
                },
                {
                  data: "labour_cost",
                  render: d => `KES ${Number(d).toLocaleString()}`
                },
                {
                  data: "status",
                  className: "text-center",
                  render: d => d === 'REPAIR DONE' ?
                    '<span class="status-pill status-done"><i class="bi bi-check-circle-fill me-1"></i>REPAIR DONE</span>' :
                    '<span class="status-pill status-pending"><i class="bi bi-clock-history me-1"></i>REPAIR PENDING</span>'
                },
                {
                  data: "date"
                }
              ],
            });

            $("#userstable tbody").on("click", "tr", function() {
              $(this).toggleClass('selected').siblings().removeClass('selected');
              let isSelected = $(this).hasClass('selected');

              table.$('input[type="checkbox"]').prop('checked', false);
              $(this).find('input[type="checkbox"]').prop('checked', isSelected);

              if (isSelected) {
                let data = table.row(this).data();
                selecteduser = data.repair_job_id;
              } else {
                selecteduser = null;
              }
            });
          }

          const repairJobModal = new bootstrap.Modal(document.getElementById('repairJobModal'));
          $('#registerRepairJob').on('click', function() {
            repairJobModal.show();
          });

          $('#repairJobForm').on('submit', function(e) {
            e.preventDefault();
            const button = $('#saveRepairJob');
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
            $.ajax({
              url: '../classes/RepairJobs.php?f=save',
              method: 'POST',
              dataType: 'json',
              data: $(this).serialize(),
              success: function(resp) {
                if (resp.status === 'success') {
                  toastr.success(resp.msg);
                  $('#repairJobForm')[0].reset();
                  repairJobModal.hide();
                  table.ajax.reload(null, false);
                } else {
                  toastr.error(resp.msg || 'Could not save the repair job.');
                }
              },
              error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Could not save the repair job.');
              },
              complete: function() {
                button.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Repair Job');
              }
            });
          });

          $('.deleteuser').click(function() {
            if (selecteduser == null) {
              toastr.error("Please select a user to delete");
              return;
            }
            _conf("Are you sure to delete this system user", "delete_user");
          })

          $('.lockuser').click(function() {
            if (!selecteduser) return toastr.error("Please select a user account first");
            _conf("Account will be restricted from system access. Continue?", "lock_user");
          });

          $('.unlockuser').click(function() {
            if (!selecteduser) return toastr.error("Please select a user account first");
            _conf("Restore system access for this user?", "unlock_user");
          });

          $('.reset').click(function() {
            if (!selecteduser) return toastr.error("Select a user to reset credentials");
            _conf("Generate a temporary password for this user?", "reset_user");
          });

          $('.edit').click(function() {
            if (selecteduser == null) {
              toastr.error("Please select a user to edit");
              return;
            }
            uni_modal('Edit System User', _base_url_ + "models/users/newuser?id=" + selecteduser, 'large');
          })
          $('.assignrole').click(function() {
            if (selecteduser == null) {
              toastr.error("Please select a user to assign roles");
              return;
            }
            uni_modal('Assign User Roles', _base_url_ + "models/users/assignroles?id=" + selecteduser, 'large');
          })
          $('.assignreports').click(function() {
            if (selecteduser == null) {
              toastr.error("Please select a user to assign reports");
              return;
            }
            uni_modal('Assign User Reports', _base_url_ + "models/users/assignreports?id=" + selecteduser, 'large');
          })

          function delete_user() {
            $.post(_base_url_ + 'classes/Users?f=deleteuser', {
                id: selecteduser
              },
              function(data) {
                var resp = JSON.parse(data);
                if (resp.status == 'success') {
                  table.ajax.reload();
                  selecteduser = null;
                  $('#confirm_modal').modal('hide');
                  toastr.success(resp.msg);
                } else {
                  toastr.error(resp.msg);

                }
              }
            );
          }

          function lock_user() {
            $('#confirm_modal').modal('hide');
            uni_modal('Enter Reason', _base_url_ + "models/users/lockreason", 'large');
          }


          function unlock_user() {
            $.post(_base_url_ + 'classes/Users?f=unlockuser', {
                id: selecteduser
              },
              function(data) {
                var resp = JSON.parse(data);
                if (resp.status == 'success') {
                  table.ajax.reload();
                  selecteduser = null;
                  $('#confirm_modal').modal('hide');
                  toastr.success(resp.msg);
                } else {
                  toastr.error(resp.msg);

                }
              }
            );
          }

          function reset_user() {
            start_loader();
            $.post(_base_url_ + 'classes/Users?f=resetuser', {
                id: selecteduser
              },
              function(data) {
                var resp = JSON.parse(data);
                if (resp.status == 'success') {
                  table.ajax.reload();
                  selecteduser = null;
                  $('#confirm_modal').modal('hide');
                  end_loader();
                  toastr.success(resp.msg);
                } else {
                  end_loader();
                  toastr.error(resp.msg);

                }
              }
            );
          }
        </script>
      </div>

    </main>
    <style>
      /* 1. Modal Animations */
      .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: scale(0.95);
      }

      .modal.show .modal-dialog {
        transform: scale(1);
      }

      /* 2. Side Modal (Right Slide) */
      #uni_modal_right .modal-dialog {
        position: fixed;
        margin: auto;
        width: 400px;
        height: 100%;
        right: -400px;
        transition: right 0.3s ease-in-out;
      }

      #uni_modal_right.show .modal-dialog {
        right: 0;
      }

      #uni_modal_right .modal-content {
        height: 100%;
        border-radius: 0;
      }

      /* 3. Branding & Headers */
      .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
      }

      .modal-header.bg-danger {
        background: linear-gradient(90deg, #dc3545 0%, #a71d2a 100%) !important;
      }

      /* 4. Soft Shadows & Borders */
      .modal-content {
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      }

      /* 5. Modern Button Styling in Modals */
      .modal-footer {
        background-color: #f8fafc;
        border-top: 1px solid #edf2f7;
        padding: 0.75rem 1.5rem;
      }

      .btn-flat {
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 0.5rem 1.25rem;
      }

      /* 6. Image Viewer Customization */
      #viewer_modal .modal-content {
        background: transparent;
        box-shadow: none;
      }

      #viewer_modal img {
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
      }

      #viewer_modal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
        position: absolute;
        right: -30px;
        top: -30px;
      }
    </style>

    <div class="modal fade" id="uni_modal" role='dialog'>
      <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
        <div class="modal-content rounded-0">
          <div class="modal-header rounded-0 bg-danger text-white">
            <h5 class="modal-title fw-bold text-white"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body rounded-0 p-4">
          </div>
          <div class="modal-footer rounded-0">
            <button type="button" class="btn btn-sm btn-flat btn-danger shadow-sm" id='submit' onclick="$('#uni_modal form').submit()">
              <i class="bi bi-save me-2"></i>SAVE CHANGES
            </button>
            <button type="button" class="btn btn-sm btn-flat btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">CANCEL</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="uni_modal2" role='dialog'>
      <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
        <div class="modal-content rounded-0">
          <div class="modal-header rounded-0 border-0 pb-0">
            <h5 class="modal-title fw-bold text-muted small uppercase"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body rounded-0 p-4">
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="uni_modal_right" role='dialog'>
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content border-0 shadow">
          <div class="modal-header rounded-0 bg-dark text-white">
            <h5 class="modal-title"></h5>
            <button type="button" class="btn btn-link text-white text-decoration-none" data-bs-dismiss="modal">
              <i class="fa fa-arrow-right"></i>
            </button>
          </div>
          <div class="modal-body rounded-0">
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="viewer_modal" role='dialog'>
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          <img src="" alt="View Image" class="img-fluid">
        </div>
      </div>
    </div>

    <div class="modal fade" id="confirm_modal" role='dialog'>
      <div class="modal-dialog modal-sm modal-dialog-centered rounded-0" role="document">
        <div class="modal-content border-top border-danger border-4">
          <div class="modal-header rounded-0 bg-white">
            <h5 class="modal-title text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i><span data-i18n="areYouSure">Are you sure?</span></h5>
          </div>
          <div class="modal-body rounded-0 py-4 text-center">
            <div id="delete_content" class="fs-6"></div>
          </div>
          <div class="modal-footer rounded-0 border-0 justify-content-center pb-4">
            <button type="button" class="btn btn-danger px-4 fw-bold" id='confirm' data-i18n="yesContinue">YES, CONTINUE</button>
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" data-i18n="no">NO</button>
          </div>
        </div>
      </div>
    </div>


    <style>
      .text-primary {
        color: #dc3545 !important;
      }

      .bg-primary {
        background-color: #dc3545 !important;
        color: #ffffff !important;
      }

      .app-footer {
        background: #ffffff;
        border-top: 1px solid #e9ecef;
        padding: 20px 0;
        transition: all 0.3s ease;
      }

      .footer-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
      }

      /* Marketing Link */
      .pearl-brand-link {
        font-weight: 800;
        letter-spacing: 1px;
        transition: 0.3s;
        position: relative;
        display: inline-block;
      }

      .pearl-brand-link:hover {
        color: #dc3545 !important;
        /* Theme Red from A&S */
        transform: translateY(-2px);
      }

      /* Developer Stats / Slogan */
      .dev-slogan {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #adb5bd;
        font-weight: 600;
      }

      /* Contact Pills */
      .footer-contacts {
        display: flex;
        gap: 15px;
        margin-top: 5px;
      }

      .footer-contacts a {
        font-size: 14px;
        color: #6c757d;
        text-decoration: none;
        transition: 0.3s;
      }

      .footer-contacts a:hover {
        color: #25d366;
        /* WhatsApp Green */
      }

      @media (min-width: 768px) {
        .footer-content {
          flex-direction: row;
          justify-content: space-between;
          padding: 0 40px;
        }
      }
    </style>
    <footer class="app-footer">
      <div class="footer-content">
        <div class="text-muted small order-2 order-md-1">
          <strong>Copyright &copy; 2026</strong>
          <span class="d-none d-sm-inline">| All Rights Reserved.</span>
        </div>

        <div class="order-1 order-md-2 text-center">
          <div class="dev-slogan mb-1 animate__animated animate__fadeIn">Think of it, We Develop it.</div>
          <a href="https://pearl-host.com/" target="_blank"
            class="text-decoration-none pearl-brand-link text-primary text-uppercase">
            <i class="bi bi-gem me-1"></i> AB Solutions
          </a>
        </div>

        <div class="footer-contacts order-3 order-md-3">
          <a href="https://wa.me/256772173286" target="_blank" title="WhatsApp Support">
            <i class="bi bi-whatsapp"></i>
          </a>
          <a href="tel:+256763808854" title="Call Systems Engineer">
            <i class="bi bi-telephone-outbound"></i>
          </a>
          <a href="mailto:support@pearl-host.com" title="Email Support">
            <i class="bi bi-envelope-at"></i>
          </a>
        </div>
      </div>
    </footer>
    <!-- Footer -->

  </div>

  <script>
    $('#MainNavText').text(mainNavText != '' ? mainNavText : 'Home');
  </script>
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
    integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>

  <!-- Required Plugin(popperjs for Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

  <!-- Required Plugin(Bootstrap 5) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

  <!--sortablejs-->
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
    integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script>

  <!--apexcharts-->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
    integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>

  <!--jsvectormap-->
  <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
    integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
    integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script>

  <!-- Summernote -->
  <script src="../assets/plugins/summernote/summernote-bs4.min.js"></script>

  <script>
    start_loader();
    $(document).ready(function() {
      end_loader();
      window.viewer_modal = function($src = '') {
        var t = $src.split('.')
        t = t[1]
        if (t == 'mp4') {
          var view = $("<video src='" + $src + "' controls autoplay></video>")
        } else {
          var view = $("<img src='" + $src + "' />")
        }
        $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
        $('#viewer_modal .modal-content').append(view)
        $('#viewer_modal').modal({
          show: true,
          backdrop: 'static',
          keyboard: false,
          focus: true
        })
      }

      window.uni_modal = function($title = '', $url = '', $size = "") {
        start_loader();
        $('#uni_modal .modal-dialog').removeClass('modal-lg modal-fullscreen modal-dialog-centered');
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occurred")
          },
          success: function(resp) {
            if (resp) {
              $('#uni_modal .modal-title').html($title)
              $('#uni_modal .modal-body').html(resp)
              if ($size != '') {
                $('#uni_modal .modal-dialog').addClass('modal-lg modal-' + $size + ' modal-dialog-centered')
              } else {
                $('#uni_modal .modal-dialog').removeAttr("class").addClass(
                  "modal-dialog modal-md modal-dialog-centered")
              }
              $('#uni_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false,
                focus: true
              })
              $('#uni_modal').modal('show')
              end_loader()
            }
          }
        })
      }

      window.uni_modal2 = function($title = '', $url = '', $size = "") {
        start_loader();
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occurred")
          },
          success: function(resp) {
            if (resp) {
              $('#uni_modal2 .modal-title').html($title)
              $('#uni_modal2 .modal-body').html(resp)
              if ($size != '') {
                $('#uni_modal2 .modal-dialog').addClass('modal-lg modal-' + $size + ' modal-dialog-centered')
              } else {
                $('#uni_modal2 .modal-dialog').removeAttr("class").addClass(
                  "modal-dialog modal-md modal-dialog-centered")
              }
              $('#uni_modal2').modal({
                show: true,
                backdrop: 'static',
                keyboard: false,
                focus: true
              })
              $('#uni_modal2').modal('show')
              end_loader()
            }
          }
        })
      }


      window._conf = function($msg = '', $func = '', $params = []) {
        $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")")
        $('#confirm_modal .modal-body').html($msg)
        $('#confirm_modal').modal('show')
      }


    })
    $('.btn').click(function() {
      toastr.clear();
    })
    // Notification polling removed - Systemnotification.php not available

    function exportTable(type, mytable) {
      if (!mytable) {
        mytable = table;
      }
      table = mytable;

      if (!table) {
        toastr.error("Table not initialized yet!");
        return;
      }

      switch (type) {
        case 'csv':
          table.button('.buttons-csv').trigger();
          break;
        case 'excel':
          table.button('.buttons-excel').trigger();
          break;
        case 'pdf':
          table.button('.buttons-pdf').trigger();
          break;
        default:
          console.error("Unknown export type: " + type);
      }
    }
  </script>
<script>
let currentLanguage = localStorage.getItem('appLanguage') || 'en';
let translations = {};

// Load translations on page load
$(document).ready(function() {
  loadLanguage(currentLanguage);
});

function loadLanguage(lang) {
  $.ajax({
    url: '../assets/lang/' + lang + '.json',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      translations = data;
      currentLanguage = lang;
      localStorage.setItem('appLanguage', lang);
      $('#currentLang').text(lang.toUpperCase());
      applyTranslations();
      // Initialize table after translations are applied
      if (!table) {
        loadDataLabel();
      } else {
        // If table exists, reinitialize it
        if ($.fn.DataTable.isDataTable('#userstable')) {
          table.destroy();
        }
        loadDataLabel();
      }
    },
    error: function(xhr) {
      console.error('Error loading language file:', xhr);
      // Still initialize table even if translations fail
      if (!table) {
        loadDataLabel();
      }
    }
  });
}

function changeLanguage(lang) {
  event.preventDefault();
  loadLanguage(lang);
}

function applyTranslations() {
  $('[data-i18n]').each(function() {
    const key = $(this).data('i18n');
    if (translations[key]) {
      const element = $(this);
      
      if (element.is('input, textarea')) {
        element.attr('placeholder', translations[key]);
      } else if (element.is('label')) {
        // For labels, replace only the text, preserve the asterisk and other HTML
        const asterisk = element.find('.text-danger').clone();
        element.text(translations[key]);
        if (asterisk.length) {
          element.append(' ');
          element.append(asterisk);
        }
      } else if (element.is('option')) {
        element.text(translations[key]);
      } else {
        element.text(translations[key]);
      }
    }
  });
}
</script>
</body>

</html>