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

  .modal {
  display: none; /* Hidden by default */
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background: #fff;
  margin: 10% auto;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.close {
  float: right;
  font-size: 24px;
  cursor: pointer;
}

.pay-btn {
  width: 100%;
  padding: 12px;
  background: #108b04;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.pay-btn:hover {
  background: #0a5741;
}

/* Loading overlay styles */
.payment-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.95);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 100;
  border-radius: 10px;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #0A5741;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-timer {
  font-size: 14px;
  color: #666;
  margin-top: 10px;
}

.loading-timer span {
  font-weight: bold;
  color: #0A5741;
}


  </style>
<?= view('Dashboard/_theme') ?>
  </head>
<body>

  <div class="container">
    <header>
      <div class="links">
        <!-- HEADER: category filter links. Each calls display_products() with a category.
             🚨 NOTE: 'Personal%20Care' = "Personal Care" URL-encoded, the controller
             reads it from GET 'category' and does a LIKE filter. If the DB category
             name has different casing/spacing this link silently shows no results. -->
        <a href="<?= base_url('newsales/display_products?category=ALL') ?>">All</a>
        <a href="<?= base_url('newsales/display_products?category=Beverages') ?>">Beverages</a>
        <a href="<?= base_url('newsales/display_products?category=Snacks') ?>">Snacks</a>
        <a href="<?= base_url('newsales/display_products?category=Household') ?>">Household</a>
<a href="<?= base_url('newsales/display_products?category=Bakery') ?>">Bakery</a>
        <a href="<?= base_url('newsales/display_products?category=Personal%20Care') ?>">Personal Care</a>
      </div>
    </header>

    <aside>
      <!-- sidebar nav. 🚨 NOTE: "Cashier" and several links point at routes that
           the Routes.php maps to the Dashboard -- they are placeholders. -->
      <?= view('Dashboard/_nav', ['active' => 'newsale']) ?>
    </aside>

    <main>
      <!-- search area -->
      <div style="display:flex; gap:8px; margin-bottom:12px;">
        <!-- SEARCH FORM: sends a GET with ?search=... to newsales/search_product.
             🚨 The controller uses $this->request->getGet('search') to read it.
             If the search finds NOTHING the controller redirects back with
             a flash message ("product not found"). -->
        <form action="<?= base_url('newsales/search_product') ?>" method="get" style="flex:1; display:flex; gap:8px;">
          <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search products..." style="flex:1; padding:8px; border-radius:8px; border:1px solid #e6e7ea;">
          <button type="submit" style="padding:8px 12px; border-radius:8px; border:none; background:#0A5741; color:white;">Search</button>
        </form>
      </div>

      <!-- Flash feedback (error / message / success / validation) shown after any controller redirect -->
      <?= view('partials/_flash_messages') ?>

      <!-- Persistent notice while a PENDING payment is active in this session -->
      <?php if (!empty($pending_sale)): ?>
        <div style="background:#B8860B;color:#fff;padding:10px 14px;border-radius:8px;margin-bottom:10px;font-size:14px;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,0.18);">
          ⏳ A payment is already in progress for Sale #<?= (int)$pending_sale_id ?> &mdash; complete or cancel it before making changes.
          <a href="<?= base_url('newsales/payment_cancel') ?>" style="color:#fff;text-decoration:underline;margin-left:8px;">Cancel pending payment</a>
        </div>
      <?php endif; ?>

      <!-- product grid -->
      <div class="tablecontainer" id="product-grid">
        <?php if (!empty($product)): ?>
          <?php foreach ($product as $products): ?>
            <!-- 🚨 WARNING: the controller MUST pass 'product' (array of rows) or
                 this box never renders. If the controller sends 'products' or 'product_list'
                 (like display_products does), this loop is skipped and you see
                 "No products found." -->
            <div class="box">
              <div class="image"></div>
              <!-- each card shows category, name and price from the product row -->
              <div style="color:#0A5741;"><b><?= esc($products['category_name']) ?></b></div>
              <div style="color:#616060;"><?= esc($products['product_name']) ?></div>
              <div style="color:#363636;">K<?= esc(number_format((float)$products['price'], 2)) ?></div>
              <!-- stock badge: green if stock > 0, red "Out of stock" otherwise -->
              <?php if ((int)$products['stock_quantity'] > 0): ?>
                <div class="stock1"><?= esc($products['stock_quantity']) ?> left</div>
              <?php else: ?>
                <div class="stock">Out of stock</div>
              <?php endif; ?>
              <!-- ADD-TO-CART FORM -->
              <!-- method="get" -> sends these fields to newsales/cart via URL query -->
              <form method="post" action="<?= base_url('newsales/cart') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= esc($products['product_id']) ?>">
                <input type="hidden" name="category_name" value="<?= esc($products['category_name']) ?>">
                <input type="hidden" name="product_name" value="<?= esc($products['product_name']) ?>">
                <!-- 🚨 SECURITY: price comes from the form, so a user CAN edit the
                     URL to pass a lower price. The controller should take price from
                     the DB instead of trusting this hidden field. -->
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
              // LOOP through session cart items; keep a running grand total
              $grandTotal = 0;
              foreach ($cart as $item):
                  // each line contributes qty * price to the grand total
                  $grandTotal += (float)$item['total'];
              ?>
                <tr>
                  <td><?= esc($item['category_name']) ?></td>
                  <td><?= esc($item['product_name']) ?></td>
                  <td>K<?= esc(number_format((float)$item['price'], 2)) ?></td>
                  <td>
                    <div style="display:flex; align-items:center; gap:4px;">
                      <!-- DECREASE QTY: posts product_id + delta=-1 to update_qty -->
                      <form method="post" action="<?= base_url('newsales/update_qty') ?>">
                        <?= csrf_field() ?>   <!-- 🚨 REQUIRED: without this CSRF token the POST is rejected -->
                        <input type="hidden" name="product_id" value="<?= esc($item['product_id']) ?>">
                        <input type="hidden" name="delta" value="-1">
                        <button type="submit" style="padding:2px 6px;">-</button>
                      </form>
                      <span><?= esc($item['qty']) ?></span>
                      <!-- INCREASE QTY: same form, delta=+1 -->
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
                    <!-- REMOVE ONE ITEM: posts product_id to newsales/remove -->
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

      <!--
        ALIGNMENT ISSUE: Checkout button flow mismatch
        PROBLEM: Checkout form posts to 'newsales/checkout' controller method
        But JavaScript intercepts the button click and shows payment modal instead (line 270-271)
        The checkout form is never actually submitted to controller

        RESULT: checkout() method is never called - modal form handles submission instead
        This creates confusion about which endpoint processes the sale

        FLOW ISSUE: Two different endpoints for same action
        CURRENT: checkout button → JS intercepts → payment modal → posts to newsales/payment
        BUT: The form says it goes to newsales/checkout

        SOLUTION: Clarify the flow
        TODO: Either:
          A) Remove the checkout form submission, button ONLY opens modal (better UX)
          B) Make checkout() prepare sale, then payment() processes payment

        RECOMMENDED: Use option A - make checkout button just open modal
        CHANGE: Replace checkout form with button that only opens modal
      -->
      <div style="display:flex; gap:8px; margin-top:8px; width:100%;">
        <!-- CLEAR CART: posts to newsales/clear, controller wipes the session cart -->
        <form method="post" action="<?= base_url('newsales/clear') ?>" style="flex:1;">
          <?= csrf_field() ?>
          <button type="submit" style="background:#d9534f; color:white; padding:8px; border-radius:8px; border:none; width:100%; cursor:pointer;">Clear</button>
        </form>
        <!-- CHECKOUT: this form SAYS it posts to newsales/checkout, but the JS below
             (line ~308) intercepts the click and OPENS THE MODAL instead.
             🚨 RESULT: checkout() in the controller NEVER runs. -->
        <form method="post" action="<?= base_url('newsales/checkout') ?>" style="flex:1;">
          <?= csrf_field() ?>
          <button type="submit" style="background:#108b04; color:white; padding:8px; border-radius:8px; border:none; width:100%; cursor:pointer;">Checkout</button>
        </form>
       
</form>
      </div>
    </div>

    <footer>Hello</footer>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
    // Set today's date in the receipt header
    // Picks the #today element and writes today's date in a friendly format.
    document.getElementById('today').innerText = new Date().toLocaleDateString(undefined, { day:'numeric', month:'long', year:'numeric' });

    <?php if (session()->getFlashdata('show_payment_modal')): ?>
      document.getElementById("paymentModal").style.display = "block";
    <?php endif; ?>

    // ⚠️ Close modal when X is clicked (.close is the span with &times;)
    //   `.onclick` property style works but is not the recommended pattern.
    document.querySelector(".close").onclick = function() {
      document.getElementById("paymentModal").style.display = "none";
    };

    // Close modal when clicking the dark background (outside .modal-content)
    window.onclick = function(event) {
      if (event.target == document.getElementById("paymentModal")) {
        document.getElementById("paymentModal").style.display = "none";
      }
    };

    // Handle payment form submission with loading overlay
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
      const transactionStatus = document.getElementById('transaction_status').value;
      
      // Only show loading overlay for PENDING status
      if (transactionStatus === 'PENDING') {
        document.getElementById('paymentLoadingOverlay').style.display = 'flex';
        let seconds = 0;
        const timerElement = document.getElementById('elapsedTime');
        const timerInterval = setInterval(function() {
          seconds++;
          if (timerElement) timerElement.textContent = seconds;
        }, 1000);

        // Store interval ID so we can clear it if needed (e.g., on error)
        window.paymentTimerInterval = timerInterval;
      }
      // For SUCCESS, FAILED, CANCELLED - let form submit normally without loading overlay
    });

    // Show/hide cancel button based on selected transaction status
    document.getElementById('transaction_status').addEventListener('change', function() {
      const cancelBtn = document.getElementById('cancelPaymentBtn');
      if (this.value === 'PENDING') {
        cancelBtn.style.display = 'block';
      } else {
        cancelBtn.style.display = 'none';
      }
    });

    // Handle cancel button click
    document.getElementById('cancelPaymentBtn').addEventListener('click', function() {
      if (confirm('Cancel this pending transaction?')) {
        // Clear the timer
        if (window.paymentTimerInterval) {
          clearInterval(window.paymentTimerInterval);
        }
        // Hide loading overlay
        document.getElementById('paymentLoadingOverlay').style.display = 'none';
        // Redirect to cancel endpoint
        window.location.href = '<?= base_url("newsales/payment_cancel") ?>';
      }
    });
    });
  </script>

  <!-- Payment Popup Modal -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Payment Information</h2>

    <p style="font-size: 18px; font-weight: bold; color: #0A5741; margin-bottom: 15px;">
      <!-- ⚠️ PROBLEM: `$total` is only passed to the view by News().
           search_product() and display_products() do NOT pass it.
           So when the page is loaded after using Search or a category
           link, this modal would show K0.00 even though the cart has
           items. The cart table (using $grandTotal from session) is
           correct, but this modal total is not. -->
      Total Amount: K<?= esc(number_format((float)($total ?? 0), 2)) ?>
    </p>

    <form method="post" action="<?= base_url('newsales/payment') ?>" id="paymentForm">
      <?= csrf_field() ?>   <!-- 🚨 REQUIRED on every POST form else CSRF filter rejects it -->

      <!-- PAYMENT METHOD -->
      <label for="payment_method">Payment Method</label>
      <select name="payment_method" id="payment_method" required>
        <!-- 🚨 controller reads getPost('payment_method'); value must match the DB
             naming used by your payment table (e.g. 'mobileMoney', 'card', 'cash') -->
        <option value="mobileMoney">Mobile Money</option>
        <option value="card">Card</option>
        <option value="cash">Cash</option>
        <option value="bank_transfer">Bank Transfer</option>
      </select>

      <!-- PAYMENT STATUS (used by controller to decide SUCCESS/FAILED/PENDING/CANCELLED) -->
      <label for="transaction_status">Payment Status</label>
      <select name="transaction_status" id="transaction_status" required>
        <option value="SUCCESS">Success</option>
        <option value="FAILED">Failed</option>
        <option value="PENDING">Pending</option>
        <option value="CANCELLED">Cancelled</option>
      </select>

      <!-- GATEWAY REFERENCE: unique transaction/reference code for the attempt -->
      <label for="gateway_reference">Gateway Reference</label>
      <input type="text" name="gateway_reference" id="gateway_reference" placeholder="Enter reference" required>

      <!-- 🚨 the controller also requires $total + $sale_id IN THE SESSION
           (set during checkout). Because checkout never runs, submitting this
           always redirects with "No sale found. Start a new sale." -->
      <button type="submit" class="pay-btn">Confirm Payment</button>
      <!-- Cancel button for pending transactions -->
      <button type="button" id="cancelPaymentBtn" class="pay-btn" style="margin-top: 10px; background: #d9534f; display: none;">Cancel Pending</button>
    </form>

    <!-- Loading Overlay - Hidden by default -->
    <div id="paymentLoadingOverlay" class="payment-loading-overlay" style="display: none;">
      <div class="loading-spinner"></div>
      <p>Waiting for payment gateway response...</p>
      <p class="loading-timer">Time elapsed: <span id="elapsedTime">0</span> seconds</p>
    </div>
  </div>
</div>

</body>
</html>

