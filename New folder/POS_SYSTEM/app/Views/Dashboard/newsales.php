<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>POS — New Sale</title>
  <style>
    /* --- layout --- */
    body{ background-color:#93A099; margin:0; padding:0; font-family: sans-serif; }
    .container{
      display: grid;
      grid-template-columns: 200px 1fr 300px;
      grid-template-rows: 60px 1fr 40px;
      grid-template-areas:
        "header header header"
        "sidebar main receit"
        "footer footer footer";
      gap: 10px;
      min-height:100vh;
      box-sizing:border-box;
      padding:10px;
    }
    header{ grid-area:header; display:flex; align-items:flex-end; padding-left:14%; background:whitesmoke; }
    .links{ display:flex; gap:20px; padding-bottom:10px; flex-wrap:wrap; }
    .links a{
      border:1px solid #3d3d3d; border-radius:10px; font-size:12px; padding:3px 12px; text-decoration:none; background:white; color:#5c5e5d;
    }

    aside{ grid-area:sidebar; background:white; border-radius:10px; box-shadow:2px 2px 2px rgba(0,0,0,0.2); padding:12px; box-sizing:border-box; }
    .nav{ display:flex; flex-direction:column; gap:10px; align-items:center; background:#0A5741; padding-top:20px; height:100%; border-radius:8px; }
    .nav a{ text-decoration:none; color:white; width:90%; text-align:center; padding:10px; border-radius:20px; background:#0A5741; box-shadow:2px 2px 2px rgba(0,0,0,0.15); }

    main{ grid-area:main; background:whitesmoke; border-radius:10px; padding:12px; box-sizing:border-box; overflow:auto; }
    .tablecontainer{ display:grid; grid-template-columns: repeat(4, 150px); gap:20px; padding-top:10px; overflow-y:auto; }

    .box{
      border-radius:20px; background:#e2e6e4; box-shadow:2px 2px 2px #0A5741; min-height:120px; padding:10px; display:flex; flex-direction:column; gap:8px;
    }
    .image{ width:130px; height:80px; background:whitesmoke; border-radius:12px; border:1px solid #fff; }
    .stock{ margin-left:auto; border-radius:20px; padding:3px 8px; background:rgb(233,45,45); color:white; display:inline-block; }
    .stock1{ margin-left:auto; border-radius:20px; padding:3px 8px; background:rgb(16,139,4); color:white; display:inline-block; }
    .add-to-cart{ border-radius:10px; margin-top:6px; background:#0A5741; color:white; padding:8px; border:none; cursor:pointer; width:100%; }

    .receit{ grid-area:receit; display:flex; flex-direction:column; align-items:center; background:#0A5741; color:white; border-radius:10px; padding:12px; box-sizing:border-box; min-height:100vh; }
    .TILL{ width:100%; }
    .TILL h4{ margin:6px 0; }

    /* --- Cart table --- */
    .tablecontainer1{
      width:100%;
      background:whitesmoke;
      border-radius:10px;
      padding:10px;
      box-sizing:border-box;
      color:#222;
      overflow:auto;
    }
    #cart-table{ width:100%; border-collapse:collapse; }
    #cart-table thead th{ text-align:left; padding:8px; color:#0A5741; font-weight:700; font-size:12px; border-bottom:2px solid #0A5741; }
    #cart-table td{ padding:8px; border-bottom:1px solid #e6e7ea; font-size:12px; vertical-align:middle; }
    .cart-actions button{ margin:2px; padding:4px 8px; border-radius:6px; border:none; cursor:pointer; background:#0A5741; color:white; font-size:12px; }
    .cart-actions button.remove{ background:#d9534f; }
    #cart-total{ text-align:right; font-weight:700; margin-top:8px; }

    footer{ grid-area:footer; text-align:center; color:#fff; padding:8px; }

    .message{ background:#108b04; color:#fff; padding:8px 12px; border-radius:8px; margin-bottom:10px; }

    @media (max-width:900px){
      .container{ grid-template-columns:1fr; grid-template-areas:"header" "main" "receit" "sidebar" "footer"; }
      .tablecontainer{ grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="links">
        <a href="<?= base_url('newsales/display_products?category=ALL') ?>">All</a>
        <a href="<?= base_url('newsales/display_products?category=Beverages') ?>">Beverages</a>
        <a href="<?= base_url('newsales/display_products?category=Snacks') ?>">Snacks</a>
        <a href="<?= base_url('newsales/display_products?category=Household') ?>">Household</a>
<a href="<?= base_url('newsales/display_products?category=Bakery') ?>">Bakery</a>
        <a href="<?= base_url('newsales/display_products?category=Personal%20Care') ?>">Personal Care</a>
      </div>
    </header>

    <aside>
      <div class="nav">
        <a href="<?= base_url('DashBoard/index') ?>">Dashboard</a>
        <a href="<?= base_url('newsales') ?>">New Sale</a>
        <a href="<?= base_url('sales') ?>">Sales</a>
<a href="<?= base_url('productcontroller') ?>">Products</a>
        <a href="<?= base_url('newsales') ?>">Cashier</a>
      </div>
    </aside>

    <main>
      <!-- search area -->
      <div style="display:flex; gap:8px; margin-bottom:12px;">
        <form action="<?= base_url('newsales/search_product') ?>" method="get" style="flex:1; display:flex; gap:8px;">
          <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search products..." style="flex:1; padding:8px; border-radius:8px; border:1px solid #e6e7ea;">
          <button type="submit" style="padding:8px 12px; border-radius:8px; border:none; background:#0A5741; color:white;">Search</button>
        </form>
      </div>

      <?php if (session()->getFlashdata('message')): ?>
        <div class="message"><?= esc(session()->getFlashdata('message')) ?></div>
      <?php endif; ?>

      <!-- product grid -->
      <div class="tablecontainer" id="product-grid">
        <?php if (!empty($product)): ?>
          <?php foreach ($product as $products): ?>
            <div class="box">
              <div class="image"></div>
              <div style="color:#0A5741;"><b><?= esc($products['category_name']) ?></b></div>
              <div style="color:#616060;"><?= esc($products['product_name']) ?></div>
              <div style="color:#363636;">K<?= esc(number_format((float)$products['price'], 2)) ?></div>
              <?php if ((int)$products['stock_quantity'] > 0): ?>
                <div class="stock1"><?= esc($products['stock_quantity']) ?> left</div>
              <?php else: ?>
                <div class="stock">Out of stock</div>
              <?php endif; ?>
              <form method="get" action="<?= base_url('newsales/cart') ?>">
                <input type="hidden" name="product_id" value="<?= esc($products['product_id']) ?>">
                <input type="hidden" name="category_name" value="<?= esc($products['category_name']) ?>">
                <input type="hidden" name="product_name" value="<?= esc($products['product_name']) ?>">
                <input type="hidden" name="price" value="<?= esc($products['price']) ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="add-to-cart" <?= ((int)$products['stock_quantity'] <= 0 ? 'disabled' : '') ?>>Add to Cart</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No products found.</p>
        <?php endif; ?>
      </div>
    </main>

    <div class="receit">
      <div class="TILL">
        <h4>TILL KEEP NEW SALE</h4>
        <h4 id="today"></h4>
        <div>
          Change customer
          <form method="get" action="<?= base_url('newsales/cart') ?>" style="display:inline;">
            <select id="customer-select" name="customer_id" style="margin-left:8px; padding:4px; border-radius:6px;">
              <option value="">Select customer</option>
              <?php if (!empty($get_customers)): ?>
                <?php foreach ($get_customers as $customers): ?>
                  <option value="<?= esc($customers['customer_id']) ?>"><?= esc($customers['firstname']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </form>
        </div>
      </div>

      <!-- cart table -->
      <div class="tablecontainer1">
        <table id="cart-table" aria-live="polite">
          <thead>
            <tr>
              <th>Category</th>
              <th>Product</th>
              <th>Price</th>
              <th style="width:110px">Qty</th>
              <th style="width:100px">Total</th>
              <th style="width:90px">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($cart)): ?>
              <?php
              $grandTotal = 0;
              foreach ($cart as $item):
                  $grandTotal += (float)$item['total'];
              ?>
                <tr>
                  <td><?= esc($item['category_name']) ?></td>
                  <td><?= esc($item['product_name']) ?></td>
                  <td>K<?= esc(number_format((float)$item['price'], 2)) ?></td>
                  <td>
                    <div style="display:flex; align-items:center; gap:4px;">
                      <form method="post" action="<?= base_url('newsales/update_qty') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= esc($item['product_id']) ?>">
                        <input type="hidden" name="delta" value="-1">
                        <button type="submit" style="padding:2px 6px;">-</button>
                      </form>
                      <span><?= esc($item['qty']) ?></span>
                      <form method="post" action="<?= base_url('newsales/update_qty') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= esc($item['product_id']) ?>">
                        <input type="hidden" name="delta" value="1">
                        <button type="submit" style="padding:2px 6px;">+</button>
                      </form>
                    </div>
                  </td>
                  <td>K<?= esc(number_format((float)$item['total'], 2)) ?></td>
                  <td class="cart-actions">
                    <form method="post" action="<?= base_url('newsales/remove') ?>" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="product_id" value="<?= esc($item['product_id']) ?>">
                      <button type="submit" class="remove">Remove</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align:center; color:#666;">Cart is empty</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <div id="cart-total">
          Total: K<?= esc(number_format($grandTotal ?? 0, 2)) ?>
        </div>
      </div>

      <div style="display:flex; gap:8px; margin-top:8px; width:100%;">
        <form method="post" action="<?= base_url('newsales/clear') ?>" style="flex:1;">
          <?= csrf_field() ?>
          <button type="submit" style="background:#d9534f; color:white; padding:8px; border-radius:8px; border:none; width:100%; cursor:pointer;">Clear</button>
        </form>
        <form method="post" action="<?= base_url('newsales/checkout') ?>" style="flex:1;">
          <?= csrf_field() ?>
          <button type="submit" style="background:#108b04; color:white; padding:8px; border-radius:8px; border:none; width:100%; cursor:pointer;">Checkout</button>
        </form>
      </div>
    </div>

    <footer>Hello</footer>
  </div>

  <script>
    // Set today's date in the receipt header
    document.getElementById('today').innerText = new Date().toLocaleDateString(undefined, { day:'numeric', month:'long', year:'numeric' });
  </script>
</body>
</html>

