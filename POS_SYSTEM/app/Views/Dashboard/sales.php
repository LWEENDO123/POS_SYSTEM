<?php
/** @var array $category */
/** @var array $products */
/** @var array $cart */
/** @var float $total_amount */
/** @var int $total_items */
/** @var string $search */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale — Tillkeep POS</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin:0; padding:0; height:100%; overflow:hidden; font-family:Arial,sans-serif; background:#f8f9f7; color:#1f2937; }
        .app-shell { display:flex; height:100vh; }
        .sidebar { width:220px; min-width:220px; background:#0A5741; color:white; padding:20px 0; }
        .sidebar ul { list-style:none; padding:0; margin:0; }
        .sidebar li { padding:14px 24px; cursor:pointer; }
        .sidebar li.active { background:rgba(255,255,255,0.15); }
        .main { flex:1; display:flex; flex-direction:column; min-width:0; }
        .topbar { background:white; padding:15px 25px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; flex-shrink:0; }
        .content { flex:1; padding:20px; overflow-y:auto; }
        .pos-layout { display:flex; gap:24px; align-items:flex-start; height:100%; }
        .catalog { flex:1; display:flex; flex-direction:column; gap:16px; min-width:0; }
        .search-form { display:flex; gap:8px; }
        .search-form input { flex:1; padding:14px 20px; border:1px solid #ccc; border-radius:10px; font-size:1rem; }
        .search-form button { padding:14px 24px; border:none; border-radius:10px; background:#0A5741; color:white; font-weight:bold; cursor:pointer; flex-shrink:0; }
        .category-scroller { display:flex; gap:10px; flex-wrap:wrap; }
        .category-scroller a { padding:8px 18px; border:1px solid #ccc; border-radius:30px; text-decoration:none; color:#333; background:white; }
        .category-scroller a.active { background:#0A5741; color:white; }
        .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(165px,1fr)); gap:16px; }
        .product-card { background:white; border:1px solid #ddd; border-radius:10px; padding:12px; text-align:center; }
        .product-card:hover { border-color:#0A5741; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
        .product-name { font-weight:bold; margin:10px 0 5px; }
        .product-price { color:#0A5741; font-weight:bold; }
        .receipt-tape { width:380px; min-width:380px; background:white; border:2px solid #1f2937; border-radius:8px; display:flex; flex-direction:column; max-height:calc(100vh - 140px); position:sticky; top:0; }
        .receipt-head { padding:20px; text-align:center; border-bottom:2px dashed #ccc; flex-shrink:0; }
        .receipt-lines { flex:1; padding:20px; overflow-y:auto; background:#fafafa; min-height:150px; }
        .receipt-line { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee; }
        .receipt-totals { padding:20px; border-top:2px dashed #ccc; flex-shrink:0; }
        .totals-row { display:flex; justify-content:space-between; margin:8px 0; }
        .grand-total { font-size:1.4rem; font-weight:bold; color:#0A5741; }
        .btn { padding:14px 20px; border:none; border-radius:30px; cursor:pointer; font-weight:bold; }
        .btn-primary { background:#0A5741; color:white; width:100%; }
        .btn-danger { background:#ef4444; color:white; width:100%; }
        .flash-msg { background:#d4edda; color:#155724; padding:12px 20px; border-radius:8px; margin-bottom:12px; }
        .flash-msg.error { background:#f8d7da; color:#721c24; }
        .receipt-empty { text-align:center; color:#888; margin-top:50px; }
        .receipt-actions { padding:15px 20px; border-top:1px solid #ddd; flex-shrink:0; }
        .receipt-actions form + form { margin-top:10px; }
    </style>
</head>
<body>
<div class="app-shell">
    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li onclick="location.href='<?= site_url('DashBoard/index') ?>'">Dashboard</li>
            <li class="active" onclick="location.href='<?= site_url('DashBoard/sales') ?>'">New Sale</li>
            <li onclick="location.href='<?= site_url('sales') ?>'">Sales</li>
            <li onclick="location.href='<?= site_url('products') ?>'">Products</li>
            <li onclick="location.href='<?= site_url('customers') ?>'">Customers</li>
            <li onclick="location.href='<?= site_url('cashiers') ?>'">Cashiers</li>
        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <div class="topbar">
            <h1>New Sale</h1>
            <div><strong>Mwansa Tembo</strong></div>

        <div class="content">
            <!-- Flash messages -->
            <?php if ($msg = session()->getFlashdata('message')): ?>
                <div class="flash-msg"><?= esc($msg) ?></div>
            <?php endif; ?>
            <?php if ($err = session()->getFlashdata('error')): ?>
                <div class="flash-msg error"><?= esc($err) ?></div>
            <?php endif; ?>

            <div class="pos-layout">
                <!-- LEFT: Product Catalog -->
                <div class="catalog">
                    <form method="GET" class="search-form" action="<?= site_url('DashBoard/sales') ?>">
                        <input type="text" name="search" placeholder="Search products..." value="<?= esc($search ?? '') ?>">
                        <button type="submit">Search</button>
                    </form>

                    <div class="category-scroller">
                        <a href="<?= site_url('DashBoard/sales') ?>" class="active">All</a>
                        <?php if (!empty($category)): ?>
                        <?php foreach ($category as $cat): ?>
                            <a href="<?= site_url('DashBoard/sales?category=' . urlencode($cat['category_name'])) ?>"><?= esc($cat['category_name']) ?></a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="product-grid">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <div class="product-card">
                                    <div style="height:110px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:3rem;">📦</div>
                                    <div class="product-name"><?= esc($product['product_name']) ?></div>
                                    <div><?= esc($product['category'] ?? '') ?></div>
                                    <div class="product-price">K<?= number_format($product['price'],2) ?></div>
                                    <button type="button"
                                            class="btn btn-primary add-to-cart-btn"
                                            style="margin-top:10px;width:100%;padding:8px;"
                                            data-product-id="<?= $product['product_id'] ?>"
                                            data-product-name="<?= esc($product['product_name']) ?>"
                                            data-price="<?= $product['price'] ?>">
                                        Add to Sale
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No products found.</p>
                        <?php endif; ?>
                    </div><!-- /catalog -->

                <!-- RIGHT: Receipt Tape -->
                <div class="receipt-tape" id="receipt-tape">
                    <div class="receipt-head">
                        <div><strong>Tillkeep · Sale #NEW</strong></div>
                        <div><?= date('M d, g:i A') ?></div>
                        <div style="margin-top:10px;padding:10px;background:#f8fafc;border-radius:8px;">Walk-in Customer</div>

                    <div class="receipt-lines" id="receipt-lines">
                        <?php if (!empty($cart)): ?>
                            <?php foreach ($cart as $item): ?>
                                <div class="receipt-line">
                                    <div>
                                        <strong><?= esc($item['product_name']) ?></strong>
                                        <br><small>K<?= number_format($item['price']??0,2) ?> × <?= $item['qty']??1 ?></small>
                                    </div>
                                    <div style="text-align:right;">
                                        K<?= number_format(($item['price']??0)*($item['qty']??1),2) ?>
                                    </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="receipt-empty">No items yet — click a product to add</p>
                        <?php endif; ?>
                    </div>

                    <div class="receipt-totals">
                        <div class="totals-row"><span>Items</span><span id="total-items"><?= $total_items ?? 0 ?></span></div>
                        <div class="totals-row grand-total"><span>Total</span><span id="total-amount">K<?= number_format($total_amount ?? 0, 2) ?></span></div>

                    <div class="receipt-actions">
                        <form method="POST" action="<?= site_url('DashBoard/checkout') ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary" id="checkout-btn" <?= empty($cart) ? 'disabled' : '' ?>>Charge Customer</button>
                        </form>
                        <form method="POST" action="<?= site_url('DashBoard/clear_cart') ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger" id="clear-cart-btn" <?= empty($cart) ? 'disabled' : '' ?>>Clear Sale</button>
                        </form>
                    </div><!-- /receipt-tape -->
            </div><!-- /pos-layout -->
        </div><!-- /content -->
    </div><!-- /main -->
</div><!-- /app-shell -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var formData = new FormData();
            formData.append('product_id',   this.dataset.productId);
            formData.append('product_name', this.dataset.productName);
            formData.append('price',        this.dataset.price);
            formData.append('qty',          1);

            fetch("<?= site_url('DashBoard/add_to_cart') ?>", {
                method: "POST",
                headers: { "X-Requested-With": "XMLHttpRequest" },
                body: formData
            })
            .then(function() {
                window.location.href = "<?= site_url('DashBoard/sales') ?>";
            })
            .catch(function(err) {
                console.error('Add to cart error:', err);
            });
        });
    });
});
</script>
</body>
</html>
