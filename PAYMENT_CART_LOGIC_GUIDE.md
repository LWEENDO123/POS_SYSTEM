# POS System: Payment, Cart & Session Logic Guide
## A Step-by-Step Learning Guide for Building Cart & Payment Systems

This guide explains HOW and WHY each part of your cart and payment system works. Use this to build similar features from scratch in future projects.

---

## 📌 OVERVIEW: The Complete Flow

```
User Login 
    ↓
1. ADD TO CART (cart endpoint)
    ↓
2. VIEW CART (products displayed with cart items)
    ↓
3. CHECKOUT (validate cart, prepare for payment)
    ↓
4. PAYMENT MODAL (show payment form)
    ↓
5. PAYMENT PROCESSING (create sale, update inventory)
    ↓
6. SUCCESS/FAILED RESPONSE
```

---

## 🛒 PART 1: BUILDING THE SHOPPING CART

### What is a Session Cart?
A **session** is temporary storage per user. When you add items to cart, they stay until:
- User clears cart
- User completes purchase
- Session expires (user closes browser)

### Step 1: Add Product to Cart

**File:** `app/Controllers/newsales.php` → `cart()` method

**Flow:**
```php
// User clicks "Add to Cart" button
// Form sends: product_id, product_name, price, qty

// Step 1: Get data from form
$product_id = $this->request->getGet('product_id');
$product_name = $this->request->getGet('product_name');
$price = $this->request->getGet('price');
$qty = $this->request->getGet('qty') ?? 1;  // Default qty = 1

// Step 2: Get existing cart from session (if any)
$cart = session()->get('cart') ?? [];  // [] = empty array if no cart exists

// Step 3: Check if product already in cart
$found_product = false;
foreach ($cart as $index => $item) {
    if ($item['product_id'] == $product_id) {
        // Product exists! Just increase qty
        $cart[$index]['qty'] += $qty;
        $cart[$index]['total'] = $cart[$index]['qty'] * $cart[$index]['price'];
        $found_product = true;
        break;
    }
}

// Step 4: If product not in cart, add it
if (!$found_product) {
    $total_price = $qty * $price;  // Calculate line total
    
    $cart[] = [
        'product_id'  => $product_id,
        'product_name'=> $product_name,
        'price'       => $price,
        'qty'         => $qty,
        'total'       => $total_price  // qty * price
    ];
}

// Step 5: Save updated cart back to session
session()->set('cart', $cart);

// Step 6: Redirect back to cart page
return redirect()->to('/newsales');
```

### Key Points:
- ✅ Check if item already exists before adding
- ✅ Calculate `total = qty * price` for each line
- ✅ Always save cart back to session after changes
- ❌ Don't create database records yet (just session storage)

---

### Step 2: Display Cart in View

**File:** `app/Views/Dashboard/newsales.php`

**Flow:**
```html
<!-- Get cart from view data -->
<?php if (!empty($cart)): ?>
    <?php 
    $grandTotal = 0;
    foreach ($cart as $item):
        $grandTotal += $item['total'];  // Sum up all line totals
    ?>
        <tr>
            <td><?= $item['product_name'] ?></td>
            <td><?= $item['qty'] ?></td>
            <td>K<?= number_format($item['total'], 2) ?></td>
            <!-- Buttons to remove, increase qty, decrease qty -->
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Display grand total -->
Total: K<?= number_format($grandTotal ?? 0, 2) ?>
```

### Key Points:
- ✅ Loop through cart items from session
- ✅ Calculate `grandTotal` by summing all item totals
- ✅ This is the amount customer must pay

---

### Step 3: Update Quantity in Cart

**File:** `app/Controllers/newsales.php` → `update_qty()` method

**Flow:**
```php
$session = session();
$cart = session()->get('cart') ?? [];

// Get product_id and quantity change (delta)
$product_id = $this->request->getPost('product_id');
$delta = (int) $this->request->getPost('delta');  // +1 or -1

// Find product in cart and update
foreach ($cart as $index => $item) {
    if ($item['product_id'] == $product_id) {
        $cart[$index]['qty'] += $delta;  // +1 or -1
        
        // Don't let qty go below 1
        if ($cart[$index]['qty'] < 1) {
            $cart[$index]['qty'] = 1;
        }
        
        // Recalculate line total
        $cart[$index]['total'] = $cart[$index]['qty'] * $cart[$index]['price'];
        break;
    }
}

// Save updated cart
session()->set('cart', $cart);
return redirect()->to('/newsales');
```

### Key Points:
- ✅ Find item by product_id
- ✅ Update qty by delta (+1 or -1)
- ✅ Recalculate total after qty changes
- ✅ Keep qty >= 1 (never go to 0)

---

### Step 4: Remove Item from Cart

**File:** `app/Controllers/newsales.php` → `remove()` method

**Flow:**
```php
$session = session();
$cart = session()->get('cart') ?? [];
$product_id = $this->request->getPost('product_id');

// Find and remove the item
foreach ($cart as $index => $item) {
    if ($item['product_id'] == $product_id) {
        unset($cart[$index]);  // Remove from array
        break;
    }
}

// Reindex array (important!)
$cart = array_values($cart);  // Makes array 0,1,2... instead of 0,2,3

// Save updated cart
session()->set('cart', $cart);
return redirect()->to('/newsales');
```

### Key Points:
- ✅ Use `unset()` to remove from array
- ✅ Use `array_values()` to reindex array
- ✅ Without reindex, foreach loops may skip items

---

### Step 5: Clear Entire Cart

**File:** `app/Controllers/newsales.php` → `clear()` method

**Flow:**
```php
$session = session();
$session->remove('cart');  // Removes 'cart' key from session

return redirect()->to('/newsales')->with('message', 'Cart cleared!');
```

### Key Points:
- ✅ Use `session()->remove()` to delete key
- ✅ This clears entire cart at once

---

## 💳 PART 2: BUILDING THE PAYMENT SYSTEM

### Understanding the Payment Flow

**Timeline:**
```
Cart populated in session
    ↓
User clicks "Checkout"
    ↓
Newsale() method called
    ├─ Calculate cart total
    ├─ Create Sale record (status = PENDING)
    ├─ Reduce product stock
    ├─ Create SaleItem records
    └─ Store sale_id and total in session
    ↓
View shows payment modal
    ↓
User submits payment form
    ↓
payment() method called
    ├─ Validate payment data
    ├─ Create PaymentTransaction record
    ├─ If SUCCESS: Update sale status to PAID
    └─ If FAILED: Update sale status to FAILED
    ↓
Clear cart from session
    ↓
Redirect to success/failure page
```

---

### Step 1: Checkout - Prepare Sale

**File:** `app/Controllers/newsales.php` → `Newsale()` method

**Flow:**
```php
public function Newsale()
{
    // Check if user is logged in
    if (!session()->get('logged_in')) {
        return redirect()->to('userlogin');
    }
    
    $session = session();
    $cart = $session->get('cart') ?? [];
    
    // Validate cart is not empty
    if (empty($cart)) {
        return redirect()->to('/newsales')->with('message', 'Cart is empty!');
    }
    
    // Calculate total from cart items
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['total'];  // Sum all line totals
    }
    
    // Store total in session for payment method
    $session->set('total', $total);
    
    // Create Sale record in database
    $sale_model = new SaleModel();
    $sale_id = $sale_model->insert([
        'sale_number'     => date('YmdHis') . mt_rand(1000, 9999),  // Unique ID
        'status'          => 'PENDING',  // Not paid yet!
        'total_amount'    => $total,     // Calculated total
        'sale_date'       => date('Y-m-d H:i:s'),
        'handled_by_user' => $session->get('username'),
    ]);
    
    // Store sale_id in session for payment method to use
    $session->set('sale_id', $sale_id);
    
    // Reduce product stock for each cart item
    $product_model = new ProductModel();
    foreach ($cart as $item) {
        $product = $product_model->find($item['product_id']);
        $current_stock = $product['stock_quantity'];
        $new_stock = $current_stock - $item['qty'];
        
        // Check if enough stock
        if ($new_stock < 0) {
            return redirect()->back()->with('message', 'Insufficient stock!');
        }
        
        // Update stock in database
        $product_model->update($item['product_id'], [
            'stock_quantity' => $new_stock
        ]);
    }
    
    // Create SaleItem records for each cart item
    $sale_item_model = new SaleItemModel();
    foreach ($cart as $item) {
        $sale_item_model->insert([
            'sale_id'      => $sale_id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'unite_price'  => $item['price'],
            'subtotal'     => $item['total'],
            'added_by_user'=> $session->get('username')
        ]);
    }
    
    // Return view with cart and total
    return view('DashBoard/newsales', [
        'username'  => $session->get('username'),
        'product'   => $products,
        'cart'      => $cart,
        'total'     => $total  // Pass total to view so modal can display it
    ]);
}
```

### Key Points:
- ✅ Validate cart is not empty
- ✅ Calculate total by summing cart items
- ✅ Store total AND sale_id in session (payment method needs them)
- ✅ Create Sale record with status = PENDING
- ✅ Reduce product stock immediately
- ✅ Create SaleItem records for each line
- ❌ Don't create PaymentTransaction yet (wait for actual payment)

### Why Status = PENDING?
```
PENDING = Sale created but not paid yet
  ↓
If payment SUCCESS → Change to PAID
If payment FAILED → Change to FAILED
```

---

### Step 2: Show Payment Modal

**File:** `app/Views/Dashboard/newsales.php`

**Flow:**
```html
<!-- Payment Modal (hidden by default) -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <h2>Payment Information</h2>
        
        <!-- Display the amount customer must pay -->
        <p><strong>Total: K<?= number_format($total ?? 0, 2) ?></strong></p>
        
        <!-- Payment form -->
        <form method="post" action="<?= base_url('newsales/payment') ?>">
            <?= csrf_field() ?>
            
            <!-- Payment Method -->
            <label>Payment Method</label>
            <select name="payment_method" required>
                <option value="mobileMoney">Mobile Money</option>
                <option value="card">Card</option>
                <option value="cash">Cash</option>
            </select>
            
            <!-- Transaction Status (how payment went) -->
            <label>Payment Status</label>
            <select name="transaction_status" required>
                <option value="SUCCESS">Success</option>
                <option value="FAILED">Failed</option>
            </select>
            
            <!-- Gateway Reference (for tracking) -->
            <label>Gateway Reference</label>
            <input type="text" name="gateway_reference" placeholder="Enter reference" required>
            
            <button type="submit">Confirm Payment</button>
        </form>
    </div>
</div>

<!-- JavaScript to show modal when user clicks Checkout -->
<script>
    // When checkout button clicked
    document.querySelector("button[type='submit']").addEventListener("click", function(e) {
        e.preventDefault();  // Don't submit form yet
        document.getElementById("paymentModal").style.display = "block";  // Show modal
    });
    
    // Close modal when X is clicked
    document.querySelector(".close").onclick = function() {
        document.getElementById("paymentModal").style.display = "none";
    };
</script>
```

### Key Points:
- ✅ Display `$total` so customer knows what they're paying
- ✅ Three form fields: payment_method, transaction_status, gateway_reference
- ✅ Use modal (popup) for better UX
- ✅ Form posts to `newsales/payment` endpoint

---

### Step 3: Process Payment

**File:** `app/Controllers/newsales.php` → `payment()` method

**Flow:**
```php
public function payment()
{
    $session = session();
    $sale_id = $session->get('sale_id');
    $total = $session->get('total');
    
    // Validate we have a valid sale
    if (!$sale_id || !$total) {
        return redirect()->to('/newsales')->with('message', 'No sale found!');
    }
    
    // Get payment data from form
    $payment_method = $this->request->getPost('payment_method');
    $transaction_status = $this->request->getPost('transaction_status');
    $gateway_reference = $this->request->getPost('gateway_reference');
    
    // Validate form data
    if (empty($payment_method) || empty($transaction_status) || empty($gateway_reference)) {
        return redirect()->back()->with('message', 'Please fill all fields!');
    }
    
    // Create PaymentTransaction record
    $payment_transaction_model = new PaymentTransaction();
    $transaction_id = $payment_transaction_model->insert([
        'sale_id'            => $sale_id,
        'payment_method'     => $payment_method,
        'transaction_status' => $transaction_status,
        'gateway_reference'  => $gateway_reference,
        'amount_attempted'   => $total,
    ]);
    
    // Handle payment result
    if ($transaction_status === 'SUCCESS') {
        // Create Payment record
        $payment_model = new PaymentModel();
        $payment_model->insert([
            'sale_id'           => $sale_id,
            'payment_method'    => $payment_method,
            'payment_status'    => 'SUCCESS',
            'processed_by_user' => $session->get('username'),
            'payment_date'      => date('Y-m-d H:i:s'),
        ]);
        
        // Update Sale status to PAID
        $sale_model = new SaleModel();
        $sale_model->update($sale_id, ['status' => 'PAID']);
        
        // Clear cart from session (sale complete!)
        $session->remove('cart');
        $session->remove('total');
        $session->remove('sale_id');
        
        return redirect()->to('/newsales')->with('message', 'Payment successful!');
    }
    else if ($transaction_status === 'FAILED') {
        // Update Sale status to FAILED
        $sale_model = new SaleModel();
        $sale_model->update($sale_id, ['status' => 'FAILED']);
        
        return redirect()->to('/newsales')->with('message', 'Payment failed!');
    }
}
```

### Key Points:
- ✅ Validate sale_id and total exist in session
- ✅ Get payment data from form
- ✅ Create PaymentTransaction record
- ✅ If SUCCESS: Create Payment record, update Sale status to PAID, clear session
- ✅ If FAILED: Update Sale status to FAILED, keep session (user can retry)
- ❌ Don't modify inventory (already done in Newsale())

---

## 💾 PART 3: UNDERSTANDING SESSION DATA

### What Data Goes in Session?

```php
// After user adds items to cart:
session()->set('cart', $cart_array);
// $cart_array = [
//     0 => ['product_id' => 1, 'product_name' => 'Coke', 'qty' => 2, 'price' => 10000, 'total' => 20000],
//     1 => ['product_id' => 5, 'product_name' => 'Bread', 'qty' => 1, 'price' => 5000, 'total' => 5000],
// ]

// After checkout:
session()->set('sale_id', $sale_id);  // ID of sale created in database
session()->set('total', $total);      // Total amount to be paid

// After payment:
session()->remove('cart');    // Delete cart
session()->remove('sale_id'); // Delete sale_id
session()->remove('total');   // Delete total
```

### Why Store in Session?

| Data | Why Session? | Why NOT Database Yet? |
|------|-------------|-------|
| **cart** | Temporary (user might close browser) | User hasn't paid yet |
| **sale_id** | Needs to pass between Newsale() and payment() methods | Sale record exists but not finalized |
| **total** | Quick access in payment form | Already in SaleItem records |

---

## 🔄 PART 4: COMPLETE EXAMPLE WALKTHROUGH

### Scenario: User buys 2 Cokes for 10,000 each

**Step 1: Add to Cart**
```
Action: User clicks "Add Coke" with qty=2, price=10000
Cart after: { product_id: 1, product_name: 'Coke', qty: 2, price: 10000, total: 20000 }
Session: {'cart': [above]}
```

**Step 2: View Cart**
```
Display: 
  Coke × 2    20,000
  ---
  Total:      20,000
```

**Step 3: Checkout (Newsale)**
```
- Calculate total from cart: 20,000
- Create Sale: { id: 100, sale_number: 20260813123045abcd, status: PENDING, total: 20,000 }
- Reduce stock: Coke stock: 50 → 48
- Create SaleItem: { sale_id: 100, product_id: 1, qty: 2, price: 10000, subtotal: 20000 }
- Session: {'cart': [...], 'sale_id': 100, 'total': 20000}
```

**Step 4: Payment Modal Shows**
```
Modal displays:
  Total: K20,000.00
  Payment Method: [dropdown]
  Payment Status: [dropdown]
  Gateway Reference: [text]
```

**Step 5: Payment (SUCCESS)**
```
- Create PaymentTransaction: { sale_id: 100, payment_method: mobileMoney, status: SUCCESS }
- Create Payment: { sale_id: 100, payment_status: SUCCESS }
- Update Sale: { status: PENDING → PAID }
- Clear session: Delete cart, sale_id, total
- Message: "Payment successful!"
```

---

## ❌ Common Mistakes to Avoid

| Mistake | Problem | Solution |
|---------|---------|----------|
| Don't calculate total each time | Qty might be wrong | Recalculate after every qty change |
| Don't forget array_values() after unset | Array has gaps, foreach skips items | Always reindex after removing |
| Don't create PaymentTransaction in Newsale() | Wastes DB records with dummy data | Create only in payment() method |
| Don't trust session total | Session might be null or wrong | Calculate total from cart items |
| Don't update inventory twice | Stock gets corrupted | Update in Newsale() only |
| Don't clear cart before payment succeeds | Can't retry if payment fails | Clear cart AFTER payment success |

---

## 🚀 BUILDING THIS FROM SCRATCH: Your Checklist

When building a new POS system, follow this order:

- [ ] Create Product table with `stock_quantity` column
- [ ] Create Sale table with `status` column (PENDING, PAID, FAILED)
- [ ] Create SaleItem table (links Sale to Products)
- [ ] Create Payment table (records successful payments)
- [ ] Create PaymentTransaction table (tracks all payment attempts)
- [ ] Build `cart()` method to add items to session
- [ ] Build view to display cart items
- [ ] Build `update_qty()` and `remove()` methods
- [ ] Build `Newsale()` method to create Sale record
- [ ] Build payment modal in view
- [ ] Build `payment()` method to create Payment record
- [ ] Add payment route to Routes.php
- [ ] Test with SUCCESS and FAILED scenarios
- [ ] Test stock reduction
- [ ] Test cart clearing after payment

---

## 📚 Key Code Snippets to Remember

### Session Operations
```php
// Store data
session()->set('key', $value);

// Retrieve data
$value = session()->get('key');

// Get with default
$value = session()->get('key') ?? 'default';

// Delete data
session()->remove('key');

// Delete all
session()->destroy();
```

### Array Operations
```php
// Add to array
$array[] = $new_item;

// Remove from array
unset($array[$index]);

// Reindex array
$array = array_values($array);

// Loop and modify
foreach ($array as $index => $item) {
    $array[$index]['qty'] = 5;  // Modify item
}
```

### CodeIgniter Redirects
```php
// Redirect with message
return redirect()->to('/newsales')->with('message', 'Success!');

// Get message in view
if (session()->getFlashdata('message')): ?>
    <p><?= session()->getFlashdata('message') ?></p>
<?php endif;
```

---

Hope this guide helps you understand the logic! Build similar systems step-by-step, and you'll master this pattern. 🎉
