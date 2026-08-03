<?php header('Content-Type: text/css'); ?>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&display=swap');
*{font-family:'DM Sans',sans-serif;}

.garage-sidebar{position:fixed;z-index:1045;top:0;left:0;width:260px;height:100vh;overflow:hidden;display:flex;flex-direction:column;background:linear-gradient(180deg,#4015bf 0%,#29106f 100%);transition:transform .28s ease;box-shadow:5px 0 24px rgba(21,5,64,.16);}
.garage-sidebar.is-collapsed{transform:translateX(-100%);}
.app-header{margin-left:260px;transition:margin-left .28s ease;}
.app-main{margin-left:260px!important;transition:margin-left .28s ease;}
body.garage-sidebar-collapsed .app-header,body.garage-sidebar-collapsed .app-main{margin-left:0!important;}
.garage-sidebar .sidebar-brand{display:flex;align-items:center;gap:10px;padding:19px 18px;color:#fff;text-decoration:none;background:rgba(0,0,0,.15);border-bottom:1px solid rgba(255,255,255,.1);flex-shrink:0;}
.garage-sidebar .brand-mark{width:39px;height:39px;border-radius:10px;display:grid;place-items:center;background:#f5ad2b;color:#301078;font-size:20px;flex-shrink:0;}
.garage-sidebar .brand-name{font-size:15px;line-height:1.1;font-weight:800;}
.garage-sidebar .brand-name small{display:block;margin-top:4px;color:#cfbefd;font-size:9px;letter-spacing:1px;}
.garage-sidebar .nav-sidebar{padding:14px 7px;overflow-y:auto;flex:1;scrollbar-width:none;}
.garage-sidebar .nav-sidebar::-webkit-scrollbar{display:none;}
.garage-sidebar .nav-item{margin:2px 0;}
.garage-sidebar .nav-link{display:flex;align-items:center;min-height:46px;gap:12px;padding:10px 18px;color:rgba(255,255,255,.88)!important;font-size:14.5px;transition:background .18s;}
.garage-sidebar .nav-link:hover,.garage-sidebar .nav-link.active{background:rgba(154,109,255,.35);color:#fff!important;}
.garage-sidebar .nav-link i:first-child{width:26px;font-size:20px;text-align:center;flex-shrink:0;}
.garage-sidebar .nav-link .nav-arrow{margin-left:auto;font-size:13px;opacity:.6;}
.garage-sidebar .nav-header{font-size:.65rem!important;text-transform:uppercase;letter-spacing:1.8px;font-weight:700;color:rgba(189,170,255,.7)!important;padding:18px 18px 5px!important;}
.garage-sidebar .sidebar-footer{padding:13px 18px;border-top:1px solid rgba(255,255,255,.12);color:#d2c7f1;font-size:11px;flex-shrink:0;}
.garage-sidebar .sidebar-footer i{color:#f5ad2b;}
@media(max-width:991px){.app-header,.app-main{margin-left:0!important;}.garage-sidebar{transform:translateX(-100%);}.garage-sidebar.is-open{transform:translateX(0);}}

.status-pill{padding:.28em .75em;font-size:.68rem;font-weight:700;text-transform:uppercase;border-radius:50px;}
.status-done{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;}
.status-pending{background:#fff4d8;color:#9a5a00;border:1px solid #ffe3a2;}
.action-group{border-right:1px solid #e2e8f0;padding-right:1rem;margin-right:1rem;}
.form-check-input:checked{background-color:#4f46e5;border-color:#4f46e5;}
.text-primary{color:#dc3545!important;}
.bg-primary{background-color:#dc3545!important;color:#fff!important;}
.app-footer{background:#fff;border-top:1px solid #e9ecef;padding:18px 0;}
.footer-content{display:flex;flex-direction:column;align-items:center;gap:8px;}
@media(min-width:768px){.footer-content{flex-direction:row;justify-content:space-between;padding:0 40px;}}
.footer-contacts a{font-size:14px;color:#6c757d;text-decoration:none;margin-left:12px;}
.footer-contacts a:hover{color:#25d366;}
