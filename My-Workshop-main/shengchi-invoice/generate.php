<?php
session_start();
$_SESSION['last_form'] = $_POST;

function v($k, $d = '') { return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : $d; }
function num($x) { return number_format((float)$x, 2); }

$parts = $_POST['part'] ?? [];
$descs = $_POST['desc'] ?? [];
$vats  = $_POST['vat'] ?? [];
$qtys  = $_POST['qty'] ?? [];
$prices= $_POST['price'] ?? [];

$rows = [];
$grandTaxable = 0; $grandTax = 0; $grandTotal = 0;

for ($i = 0; $i < count($parts); $i++) {
    if (trim($parts[$i]) === '') continue;
    $qty = (float)($qtys[$i] ?? 0);
    $price = (float)($prices[$i] ?? 0);
    $vatPct = (float)($vats[$i] ?? 0);
    $taxable = $qty * $price;
    $tax = $taxable * ($vatPct / 100);
    $total = $taxable + $tax;
    $grandTaxable += $taxable;
    $grandTax += $tax;
    $grandTotal += $total;
    $rows[] = [
        'part' => htmlspecialchars($parts[$i]),
        'desc' => htmlspecialchars($descs[$i] ?? ''),
        'vat' => $vatPct,
        'qty' => $qty,
        'price' => $price,
        'taxable' => $taxable,
        'tax' => $tax,
        'total' => $total,
    ];
}

$docDate = !empty($_POST['doc_date']) ? date('M d Y', strtotime($_POST['doc_date'])) : date('M d Y');

$counterFile = __DIR__ . '/data/counter.txt';
$lastUsed = 2197;
if (is_file($counterFile)) {
    $tmp = (int)trim(file_get_contents($counterFile));
    if ($tmp > 0) $lastUsed = $tmp;
}
$submittedNo = $_POST['job_card_no'] ?? '';
preg_match('/(\d+)/', $submittedNo, $m);
$claimed = $m ? (int)$m[1] : $lastUsed + 1;
if ($claimed > $lastUsed) {
    if (!is_dir(__DIR__ . '/data')) @mkdir(__DIR__ . '/data', 0777, true);
    file_put_contents($counterFile, (string)$claimed, LOCK_EX);
}

$logoFile = null;
foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
    if (is_file(__DIR__ . '/logo/company_logo.' . $ext)) {
        $logoFile = 'logo/company_logo.' . $ext . '?v=' . @filemtime(__DIR__ . '/logo/company_logo.' . $ext);
        break;
    }
}

function find_signature($slot) {
    foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
        $f = __DIR__ . '/signatures/' . $slot . '.' . $ext;
        if (is_file($f)) {
            return 'signatures/' . $slot . '.' . $ext . '?v=' . @filemtime($f);
        }
    }
    return null;
}
$sigCustomer = find_signature('customer');
$sigAdvisor  = find_signature('advisor');
$sigCashier  = find_signature('cashier');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Estimate - Shengchi Auto</title>
<link rel="stylesheet" href="bootstrap-icons.min.css">
<style>
  @media print {
    .no-print { display:none; }
    body { padding:0; margin:0; }
  }
  body { font-family: Arial, Helvetica, sans-serif; font-size:13px; color:#111; max-width:900px; margin:20px auto; padding:0 20px; }
  .top-actions { text-align:right; margin-bottom:10px; }

  /* ── Mobile ── */
  @media (max-width: 640px) {
    body { font-size: 11px; padding: 0 10px; margin: 10px auto; }
    .top-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn { font-size: 12px; padding: 7px 12px; }
    .header-row { flex-direction: column; align-items: center; gap: 8px; text-align: center; }
    .logo { width: 90px; height: 90px; }
    .company-name { font-size: 15px; }
    h2.title { font-size: 15px; }
    table.info, table.items, .totals-box { font-size: 10px; }
    table.info td, table.items th, table.items td, .totals-box td { padding: 4px 5px; }
    .signatures { flex-direction: column; gap: 20px; }
    .signatures div { width: 100%; }
    .totals-box { width: 100%; }
  }
  .btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 18px; color:#fff; border:none; border-radius:8px;
    cursor:pointer; font-size:13px; font-weight:600; text-decoration:none;
    box-shadow:0 3px 10px rgba(0,0,0,.12);
    transition: background .15s ease, transform .1s ease;
  }
  .btn:hover { transform: translateY(-1px); }
  .btn-blue {
    background: linear-gradient(120deg, #1d4ed8 0%, #3b82f6 100%);
    border: 1px solid #1d4ed8;
  }
  .btn-blue:hover { background: #2563eb; box-shadow:0 5px 14px rgba(37,99,235,.4); }
  .btn-gray {
    background: linear-gradient(120deg, #64748b 0%, #94a3b8 100%);
    border: 1px solid #64748b;
  }
  .btn-gray:hover { background: #475569; }
  .header { text-align:center; }
  .header-row { display:flex; align-items:center; justify-content:space-between; }
  .logo { width:160px; height:160px; object-fit:contain; }
  .company-name { font-size:20px; font-weight:bold; margin:0; }
  .company-sub { font-size:12px; margin:2px 0; }
  .company-sub b { font-weight:bold; }
  h2.title { text-align:center; font-size:18px; margin:18px 0 10px; }
  table.info { width:100%; border-collapse:collapse; margin-bottom:0; }
  table.info td { border:1px solid #333; padding:6px 8px; vertical-align:top; font-size:12px; }
  table.items { width:100%; border-collapse:collapse; margin-top:10px; }
  table.items th, table.items td { border:1px solid #333; padding:6px 8px; font-size:12px; text-align:left; }
  table.items th { background:#f0f0f0; }
  table.items td.num, table.items th.num { text-align:right; }
  .totals-box { width:280px; margin-left:auto; margin-top:12px; border-collapse:collapse; }
  .totals-box td { border:1px solid #333; padding:5px 8px; font-size:12px; }
  .totals-box td:first-child { font-weight:bold; }
  .totals-box td:last-child { text-align:right; }
  .payment-terms { margin-top:20px; font-size:12px; line-height:1.5; }
  .signatures { display:flex; justify-content:space-between; margin-top:60px; text-align:center; }
  .signatures div { width:30%; font-size:12px; }
  .signatures .sig-box { height:70px; display:flex; align-items:flex-end; justify-content:center; margin-bottom:2px; }
  .signatures img.sig-img { max-height:70px; max-width:100%; object-fit:contain; }
  .signatures .sig-line { border-top:1px solid #333; padding-top:6px; }
  .page-footer { text-align:right; font-size:11px; color:#555; margin-top:20px; }
</style>
</head>
<body>

<div class="top-actions no-print">
  <a href="index.php?edit=1" class="btn btn-gray"><i class="bi bi-arrow-left"></i>Back / Edit</a>
  <button class="btn btn-blue" onclick="window.print()"><i class="bi bi-printer-fill"></i>Print / Save as PDF</button>
</div>

<div class="header">
  <div class="header-row">
    <?php if ($logoFile): ?>
      <img class="logo" src="<?php echo $logoFile; ?>" alt="Company logo">
    <?php else: ?>
      <svg class="logo" viewBox="0 0 100 100"><circle cx="50" cy="50" r="46" fill="none" stroke="#c0392b" stroke-width="4"/><text x="50" y="58" font-size="14" text-anchor="middle" fill="#c0392b" font-family="Arial" font-weight="bold">SHENGCHI</text></svg>
    <?php endif; ?>
    <div style="flex:1;">
      <p class="company-name">SHENGCHI AUTO LTD</p>
      <p class="company-sub">5 EDINBURGH AVE, NEAR UGANDA PASSPORT COLLECTION CENTRE, KYAMBOGO, KAMPALA CITY, Central (CE).</p>
      <p class="company-sub">Branch Address: KYAMBOGO</p>
      <p class="company-sub"><b>TIN:</b> [TIN NUMBER] &nbsp; <b>VAT:</b> [VAT NUMBER]</p>
    </div>
    <?php if ($logoFile): ?>
      <img class="logo" src="<?php echo $logoFile; ?>" alt="Company logo">
    <?php else: ?>
      <svg class="logo" viewBox="0 0 100 100"><circle cx="50" cy="50" r="46" fill="none" stroke="#c0392b" stroke-width="4"/><text x="50" y="58" font-size="14" text-anchor="middle" fill="#c0392b" font-family="Arial" font-weight="bold">SHENGCHI</text></svg>
    <?php endif; ?>
  </div>
</div>

<h2 class="title">Estimate</h2>

<table class="info">
  <tr>
    <td rowspan="4" style="width:45%;">
      <b>SHENGCHI AUTO LTD</b><br>
      5 Edinburgh Ave, near Uganda Passport Collection Centre,<br>
      Kyambogo, Kampala City, Central (CE).<br>
      TIN: [TIN NUMBER]<br>
      VAT: [VAT NUMBER]<br>
      Contact: +256 777552940 0757063365<br>
      Email: shengchiauto@gmail.com
    </td>
    <td><b>RFE No:</b> <?php echo v('rfe_no'); ?></td>
    <td><b>Date:</b> <?php echo $docDate; ?></td>
  </tr>
  <tr>
    <td><b>Job Card No:</b> <?php echo v('job_card_no'); ?></td>
    <td><b>Vehicle No:</b> <?php echo v('vehicle_no'); ?></td>
  </tr>
  <tr>
    <td><b>Advisor Name:</b> <?php echo v('advisor_name'); ?></td>
    <td><b>Service Type:</b> <?php echo v('service_type'); ?></td>
  </tr>
  <tr>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td><b>Customer Name:</b> <?php echo v('customer_name'); ?><br><b>Mobile No:</b> <?php echo v('mobile_no'); ?></td>
    <td colspan="2">
      <b>Vehicle:</b> <?php echo v('vehicle_desc'); ?><br>
      <b>Kilometer:</b> <?php echo v('kilometer') ? v('kilometer') . ' km' : ''; ?><br>
      <b>Color:</b> <?php echo v('color'); ?><br>
      <b>Fuel:</b> <?php echo v('fuel') ? v('fuel') . ' L' : ''; ?><br>
      <b>Phone:</b> <?php echo v('mobile_no'); ?><br>
      <b>Email:</b> <?php echo v('cust_email'); ?>
    </td>
  </tr>
</table>

<table class="items">
  <thead>
    <tr>
      <th>#</th>
      <th>Part / Service</th>
      <th>Description</th>
      <th class="num">VAT (%)</th>
      <th class="num">Quantity</th>
      <th class="num">Unit Price (USH)</th>
      <th class="num">Taxable (USH)</th>
      <th class="num">Tax Amount</th>
      <th class="num">Total (USH)</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $i => $r): ?>
    <tr>
      <td><?php echo $i + 1; ?></td>
      <td><?php echo $r['part']; ?></td>
      <td><?php echo $r['desc']; ?></td>
      <td class="num"><?php echo $r['vat']; ?></td>
      <td class="num"><?php echo num($r['qty']); ?></td>
      <td class="num"><?php echo num($r['price']); ?></td>
      <td class="num"><?php echo num($r['taxable']); ?></td>
      <td class="num"><?php echo num($r['tax']); ?></td>
      <td class="num"><?php echo num($r['total']); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <td colspan="6" style="text-align:right;"><b>Total</b></td>
      <td class="num"><b>USH <?php echo num($grandTaxable); ?></b></td>
      <td class="num"><b>USH <?php echo num($grandTax); ?></b></td>
      <td class="num"><b>USH <?php echo num($grandTotal); ?></b></td>
    </tr>
  </tbody>
</table>

<table class="totals-box">
  <tr><td>Parts Total</td><td>USH 0.00</td></tr>
  <tr><td>Labour Total</td><td>USH <?php echo num($grandTaxable); ?></td></tr>
  <tr><td>VAT Total</td><td>USH <?php echo num($grandTax); ?></td></tr>
  <tr><td>Round off</td><td>USH <?php echo num($grandTotal); ?></td></tr>
  <tr><td>Balance</td><td>USH <?php echo num($grandTotal); ?></td></tr>
</table>

<div class="payment-terms">
  <b>PAYMENT TERMS:</b> for non cash payments, use the bank details below<br>
  Bank &nbsp; <?php echo v('bank'); ?><br>
  Branch &nbsp; <?php echo v('branch'); ?><br>
  ACC No. &nbsp; <?php echo v('acc_no'); ?><br>
  ACC NAME. &nbsp; <?php echo v('acc_name'); ?>
</div>

<div class="signatures">
  <div>
    <div class="sig-box"><?php if ($sigCustomer): ?><img class="sig-img" src="<?php echo $sigCustomer; ?>" alt="Customer signature"><?php endif; ?></div>
    <div class="sig-line">Customer / Authorized Signatory</div>
  </div>
  <div>
    <div class="sig-box"><?php if ($sigAdvisor): ?><img class="sig-img" src="<?php echo $sigAdvisor; ?>" alt="Service advisor signature"><?php endif; ?></div>
    <div class="sig-line">Service Advisor Signature</div>
  </div>
  <div>
    <div class="sig-box"><?php if ($sigCashier): ?><img class="sig-img" src="<?php echo $sigCashier; ?>" alt="Cashier signature"><?php endif; ?></div>
    <div class="sig-line">Cashier / Authorized Signature</div>
  </div>
</div>

<div class="page-footer">Page 1 of 1</div>

</body>
</html>
