<!-- app/Views/dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tillkeep Dashboard</title>
  <style>
    :root{
      --accent:#0A5741;
      --muted:#ECEFEA;
      --card:#fff;
      --danger:#AE3E37;
      --shadow: rgba(0,0,0,0.08);
    }
    *{box-sizing:border-box}
    body{ margin:0; font-family:Arial, Helvetica, sans-serif; background:var(--muted); color:#222; }

    /* Layout */
    .app-shell{ display:flex; min-height:100vh; }
    .sidebar{
      width:220px;
      background:var(--accent);
      color:#fff;
      padding:20px 12px;
      position:fixed;
      top:0; left:0; bottom:0;
    }
    .sidebar nav ul{ list-style:none; padding:0; margin:0; }
    .sidebar nav li{
      padding:12px 10px;
      border-radius:6px;
      margin-bottom:6px;
      cursor:pointer;
    }
    .sidebar nav li:hover{ background:rgba(255,255,255,0.06) }
    .sidebar .user{ margin-top:20px; font-size:13px; color:rgba(255,255,255,0.85) }

    .main{
      margin-left:240px;
      padding:20px;
      flex:1;
    }

    .section-head{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:16px;
    }
    .pill-tabs button{
      padding:8px 12px;
      border-radius:20px;
      border:1px solid #ddd;
      background:#fff;
      cursor:pointer;
      margin-right:8px;
    }
    .pill-tabs .is-active{ background:var(--accent); color:#fff; border-color:var(--accent) }

    .card{ background:var(--card); border-radius:10px; padding:16px; box-shadow:0 2px 8px var(--shadow); }

    /* Summary cards */
    .summary-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px; }
    .summary-card{ padding:16px; border-radius:10px; background:var(--card); box-shadow:0 2px 6px var(--shadow); }
    .summary-card h3{ margin:0; font-size:18px; color:var(--accent) }
    .summary-card p{ margin:8px 0 0; color:#444; font-weight:700; font-size:18px }

    /* Recent sales table */
    .table-wrap{ overflow:auto; }
    table.data-table{ width:100%; border-collapse:collapse; min-width:720px; }
    table.data-table thead th{
      background:var(--accent); color:#fff; padding:10px; text-align:left; font-weight:700;
    }
    table.data-table tbody td{ padding:10px; border-bottom:1px solid #eee; vertical-align:middle; }
    table.data-table tbody tr:hover{ background:#fafafa; }

    .num{ text-align:right; }

    /* Low stock box */
    .low-stock{ margin-top:16px; border-radius:10px; padding:12px; background:#fff; box-shadow:0 2px 6px var(--shadow); }
    .low-stock table{ width:100%; border-collapse:collapse; }
    .low-stock td{ padding:8px 6px; border-bottom:1px solid #f0f0f0; }
    .badge{ padding:6px 10px; border-radius:12px; font-weight:700; color:#fff; background:var(--danger); display:inline-block; }

    /* Manage button */
    .manage-btn{
      display:inline-block;
      padding:10px 14px;
      border-radius:10px;
      background:var(--accent);
      color:#fff;
      text-decoration:none;
      font-weight:700;
      border:2px solid #fff;
      box-shadow:0 2px 6px rgba(0,0,0,0.12);
    }

    /* Responsive */
    @media (max-width:900px){
      .summary-grid{ grid-template-columns:repeat(2,1fr); }
      .receipt-col{ display:none; }
    }
  </style>
</head>
<body>
  <div class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar" role="navigation" aria-label="Main navigation">
      <nav>
        <ul>
          <li onclick="location.href='<?= site_url('DashBoard/index') ?>'">Dashboard</li>
          <li onclick="location.href='<?= base_url('DashBoard/sales') ?>'">New Sale</li>
          <li onclick="location.href='<?= site_url('sales') ?>'">Sales</li>
          <li onclick="location.href='<?= site_url('products') ?>'">Products</li>
          <li onclick="location.href='<?= site_url('customers') ?>'">Customers</li>
          <li onclick="location.href='<?= site_url('cashiers') ?>'">Cashiers</li>
        </ul>
      </nav>

      <div class="user">
        <strong><?= esc($user['name'] ?? 'Mwansa Tembo') ?></strong><br />
        <span class="muted"><?= esc($user['role'] ?? 'Admin') ?></span>
      </div>
    </aside>

    <!-- Main content -->
    <main class="main" role="main">
      <div class="section-head">
        <div>
          <h2>Welcome to the Dashboard</h2>
          <p class="muted">Overview of sales and inventory</p>
        </div>
        <a class="manage-btn" href="<?= site_url('manage') ?>">Manage</a>
      </div>

      <!-- Summary cards -->
      <div class="summary-grid">
        <div class="summary-card card">
          <h3>Today's Sales</h3>
          <p>K<?= number_format($summary['today_sales'] ?? 0, 2) ?></p>
          <div class="muted"><?= intval($summary['todays_sales_count'] ?? 0) ?> completed</div>
        </div>

        <div class="summary-card card">
          <h3>Open Tickets</h3>
          <p><?= intval($summary['open_tickets'] ?? 0) ?></p>
          <div class="muted">awaiting checkout</div>
        </div>

        <div class="summary-card card">
          <h3>Low Stock</h3>
          <p><?= intval($summary['low_stock_count'] ?? 0) ?></p>
          <div class="muted">items need reordering</div>
        </div>

        <div class="summary-card card">
          <h3>Products</h3>
          <p><?= intval($summary['products_count'] ?? 0) ?></p>
          <div class="muted"><?= intval($summary['categories_count'] ?? 0) ?> categories</div>
        </div>
      </div>

      <!-- Recent sales and low stock -->
      <div class="card">
        <div class="table-wrap">
          <table class="data-table" aria-label="Recent sales">
            <thead>
              <tr>
                <th>Sale</th>
                <th>Date</th>
                <th>Cashier</th>
                <th>Customer</th>
                <th class="num">Items</th>
                <th class="num">Total</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recent_sales) && is_array($recent_sales)): ?>
                <?php foreach ($recent_sales as $sale): ?>
                  <tr>
                    <td><?= esc($sale['sale_id']) ?></td>
                    <td><?= esc($sale['sale_date']) ?></td>
                    <td><?= esc($sale['username'])?></td>
                    <td><?= esc($sale['firstname'] ?? 'Walk-in Customer') ?></td>
                    <td class="num"><?= intval($sale['items_count']) ?></td>
                    <td class="num">K<?= number_format($sale['total_amount'], 2) ?></td>
                    <td>
                      <?php if ($sale['status'] === 'PAID'): ?>
                        <span style="color:green;font-weight:700">PAID</span>
                      <?php elseif ($sale['status'] === 'CANCELLED'): ?>
                        <span style="color:red;font-weight:700">CANCELLED</span>
                      <?php else: ?>
                        <span style="color:orange;font-weight:700"><?= esc($sale['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="<?= site_url('sales/view/' . $sale['sale_id']) ?>">View</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="8" class="muted">No recent sales found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="low-stock card" aria-label="Low stock">
        <h4 style="margin:0 0 8px 0">Low Stock</h4>
        <table>
          <tbody>
            <?php if (!empty($low_stock) && is_array($low_stock)): ?>
              <?php foreach ($low_stock as $item): ?>
                <tr>
                  <td><?= esc($item['product_name']) ?> <span class="muted">· <?= esc($item['category']) ?></span></td>
                  <td style="text-align:right"><span class="badge"><?= intval($item['stock_quantity']) ?> left</span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="2" class="muted">No low stock items</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</body>
</html>
