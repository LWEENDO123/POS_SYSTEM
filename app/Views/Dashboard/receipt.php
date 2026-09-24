<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipts - Sales Management System</title>
  <style>
    /* CSS Reset & Variables */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    body {
      background-color: #f8f9f7;
      color: #333333;
      display: grid;
      grid-template-columns: 220px 1fr;
      grid-template-rows: 64px 1fr 40px;
      grid-template-areas: 
        "header header"
        "sidebar main"
        "footer footer";
      height: 100vh;
      overflow: hidden;
    }

    /* Top Header Bar */
    .header {
      grid-area: header;
      background-color: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 24px;
      z-index: 10;
    }

    .search-form {
      display: flex;
      gap: 8px;
      width: 100%;
      max-width: 600px;
    }

    .searchbar {
      flex: 1;
      padding: 8px 14px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-size: 0.9rem;
      outline: none;
      transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    .searchbar:focus {
      border-color: #0A5741;
      box-shadow: 0 0 0 3px rgba(10, 87, 65, 0.15);
    }

    .btn-search {
      background-color: #0A5741;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      padding: 8px 18px;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 160ms ease;
    }

    .btn-search:hover {
      background-color: #2c866d;
    }

    /* Left Sidebar */
    .sidebar {
      grid-area: sidebar;
      background-color: #0A5741;
      padding: 24px 16px;
      display: flex;
      flex-direction: column;
    }

    .nav {
      display: flex;
      flex-direction: column;
      gap: 8px;
      width: 100%;
    }

    .nav a {
      display: block;
      text-decoration: none;
      color: #ffffff;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 500;
      transition: background-color 160ms ease;
    }

    .nav a:hover,
    .nav a.active {
      background-color: #2c866d;
    }

    /* Main Content Area */
    .main {
      grid-area: main;
      padding: 24px;
      overflow-y: auto;
      background-color: #f8f9f7;
    }

    .page-view {
      max-width: 1100px;
      margin: 0 auto;
      width: 100%;
    }

    /* Card Component */
    .card {
      background-color: #ffffff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
      overflow: hidden;
    }

    .card-header {
      padding: 18px 24px;
      border-bottom: 1px solid #e2e8f0;
      background-color: #ffffff;
    }

    .card-header h3 {
      font-size: 1.25rem;
      color: #0A5741;
      font-weight: 600;
    }

    .card-body {
      padding: 0;
      overflow-x: auto;
    }

    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    .data-table th {
      background-color: #0A5741;
      color: #ffffff;
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 12px 18px;
      border: none;
    }

    .data-table td {
      padding: 14px 18px;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.9rem;
      color: #334155;
    }

    .data-table tbody tr:last-child td {
      border-bottom: none;
    }

    .data-table tbody tr:hover {
      background-color: #f1f5f9;
    }

    /* Action Buttons */
    .btn-action {
      border: none;
      border-radius: 6px;
      padding: 6px 14px;
      cursor: pointer;
      font-size: 0.85rem;
      font-weight: 500;
      transition: background-color 160ms ease;
    }

    .btn-action.btn-view {
      background-color: #0A5741;
      color: #ffffff;
      text-decoration: none;   /* FIX: it is a link now - remove underline */
      display: inline-block;
    }

    .btn-action.btn-view:hover {
      background-color: #2c866d;
    }

    /* Footer */
    .footer {
      grid-area: footer;
      background-color: #ffffff;
      border-top: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      color: #64748b;
    }
  </style>
<?= view('Dashboard/_theme') ?>
  </head>
<body>

  <!-- Header -->
  <header class="header">
    <form class="search-form" method="get" action="<?= base_url('receipts/search') ?>">
      <input type="text" class="searchbar" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search by receipt number...">
      <button type="submit" class="btn-search">Search</button>
    </form>
  </header>

  <!-- Sidebar -->
  <aside class="sidebar">
    <?= view('Dashboard/_nav', ['active' => 'receipt']) ?>
  </aside>

  <!-- Main Content -->
  <main class="main">
    <?= view('partials/_flash_messages') ?>
    <div class="page-view">
      <div class="card">
        <div class="card-header">
          <h3>Receipts</h3>
        </div>
        <div class="card-body">
          <table class="data-table">
            <thead>
              <tr>
                <th>Receipt #</th>
                <th>Sale ID</th>
                <th>Total</th>
                <th>Generated By</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="receiptsTable">
              <?php if (!empty($receipts)): ?>
                <?php foreach ($receipts as $receipt): ?>
                  <tr>
                    <td><?= esc($receipt['receipt_num']) ?></td>
                    <td><?= esc($receipt['sale_id']) ?></td>
                    <td>K<?= esc(number_format($receipt['total'], 2)) ?></td>
                    <td><?= esc($receipt['cashier_name']) ?></td>
                    <td><?= esc($receipt['date']) ?></td>
                    <td>
                      <!-- FIX: was a plain <button> with no action (clicking did
                           nothing). Now a link that opens the receipt for THIS
                           row's sale: Receipt::view_receipt renders the preview. -->
                      <!-- POPUP: opens the receipt in an on-page modal (no
                           navigation). The href is kept as a fallback for
                           no-JS browsers. -->
                      <a class="btn-action btn-view"
                         href="<?= base_url('receipts/view/'.esc($receipt['sale_id'])) ?>"
                         onclick="openReceiptModal('<?= esc($receipt['sale_id']) ?>'); return false;">View</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- FIX: this used to show a FAKE hardcoded receipt row
                     (202761 / Jane Banda) whenever the list was empty -
                     misleading. Now a proper empty state. -->
                <tr>
                  <td colspan="6" style="text-align:center; padding:40px; color:#64748b;">
                    No receipts found. Complete a sale to generate one.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    &copy; <?= date('Y') ?> Sales Management System. All rights reserved.
  </footer>

  <!-- Receipt popup (modal) - shared partial -->
  <?= view('Dashboard/_receipt_modal') ?>

</body>
</html>