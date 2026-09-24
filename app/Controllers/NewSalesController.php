<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\CategoryModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\PaymentTransaction;
use App\Models\ReceiptModel;






class NewSalesController extends BaseController
{

    

    public function showNewSale()
    {
$this->trace('showNewSale', 'ENTER | new sale page');

    

    $session=session();
    
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid sessesion or session expired');
    }
    $user=session()->get('username');

    

    
    $cart=session()->get('cart')??[];
    
   

   
    $total=0;

   foreach($cart as $item){

        
        $total += $item['total'];
    }

    
    session()->set('total',$total);

   

    
    $productModel = new ProductModel();

    
    $products = $productModel->getProductsWithCategory(50);

   

   
   

    $pending = $this->pendingSaleInfo();

    $data=[
        'user'=>$user,
        'product'=>$products,
        'total'=>$total,
        'cart'=>$cart,
        'pending_sale'=>$pending['pending'],
        'pending_sale_id'=>$pending['sale_id']
    ];

    
    

    

   
    return view('Dashboard/newsales',$data);
}


    

    public function displayProducts()
    {
$this->trace('displayProducts', 'ENTER | product display/filter');
        
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/userlogin')->with('error', 'invalid session or session expired');
        }

       
        $category = $this->request->getGet('category');

       
        $productModel = new ProductModel();

       
        
        
        if (empty($category) || strtolower($category) === 'all') {
            $products = $productModel->getProductsWithCategory(50);
        } else {
            $products = $productModel->getProductsByCategory($category, 50);
        }

     
        $cart = session()->get('cart') ?? [];

     
        
        $total = session()->get('total') ?? 0;

        
        
        $user = session()->get('username');

      
        
        $pending = $this->pendingSaleInfo();

        return view('Dashboard/newsales', [
            'user'     => $user,
            'product'  => $products,
            'cart'     => $cart,
            'total'    => $total,
            'category' => $category,
            'pending_sale'    => $pending['pending'],
            'pending_sale_id' => $pending['sale_id'],
        ]);
    }

    public function searchProducts()
    {  
$this->trace('searchProducts', 'ENTER | product search');
   
    
    $session=session();

    if(!session()->get('logged_in')){
        return redirect()->to('userlogin')->with('error','invalid session or session expired');
    }
    

   
    
    $search=$this->request->getGet('search');
  
    
    $productModel = new ProductModel();
    
    $productQuery = $productModel->searchProductsByName($search, 50);
    

   
    if(empty($search)){
        
        return redirect()->to('/newsales')->with('error','product name not found');
    }
   
    $products = $productQuery;
    
    if(!$products){
    
        return redirect()->to('/newsales')->with('error','searched product not found');
    }
    
    $cart=session()->get('cart') ?? [];
    
    $total=session()->get('total') ?? 0;

    $user=session()->get('username');

    
    
    $pending = $this->pendingSaleInfo();

    return view('Dashboard/newsales',[
        'cart'=>$cart,
        'user'=>$user,
        'total'=>$total,
        'product'=>$products,
        'search'=>$search,
        'pending_sale'=>$pending['pending'],
        'pending_sale_id'=>$pending['sale_id']
    ]);

    }

    
    public function addToCart()
    {
$this->trace('addToCart', 'ENTER | add item to cart');
   
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }

   
    if ($this->hasPendingSale()) {
        return redirect()->to('/newsales')->with('error', 'Complete or cancel the pending payment before changing the cart.');
    }

    $productId = $this->request->getPost('product_id');
    $qty        = $this->request->getPost('qty');

   
    if(!$qty){
        $qty = 1;
    }
    $qty = (int)$qty;

    if($qty < 1){

        return redirect()->back()->with('error','qty can not be less than 1');

    }

    $productModel = new ProductModel();
    
    $product = $productModel->getProductWithCategory($productId); 
    $this->trace('addToCart', 'PRODUCT LOOKUP | product_id={product_id} found={found}', [
        'product_id' => $productId,
        'found'      => $product ? 'yes' : 'no',
    ]);
    if(!$product){
        return redirect()->back()->with('error','product id not found');
    }

   
    $stock_quantity = (int)$product['stock_quantity'];

    if($qty > $stock_quantity){
        
        return redirect()->back()->with('error','insufficient stock');
    }

   
    $cart = session()->get('cart') ?? [];

  
    $item_exists = false;

    
    foreach ($cart as &$item) {

        
        if ($item['product_id'] ==  $productId) {

           
            $new_qty = (int)$item['qty'] + $qty;

           
            if ($new_qty > $stock_quantity) {
                $new_qty = $stock_quantity; 
            }

           
            $item['qty'] = $new_qty;

           
            $item['total'] = (float)$item['price'] * $new_qty;

           
            $item_exists = true;

            break;
        }
    }
    
    unset($item);

    if (!$item_exists) {
        $cart[] = [
            
            'product_id'    => $product['product_id'],
            'product_name'  => $product['product_name'],
            'category_name' => $product['category_name'],
            'qty'           => $qty,
            'price'         => (float)$product['price'],
            'total'         => (float)$product['price'] * $qty,
        ];
    }

    
    $total = 0;
    foreach ($cart as $item) {
        $total += (float)$item['total'];
    }

    
    session()->set([
        'cart'  => $cart,
        'total' => $total,
    ]);

    
    $this->trace('addToCart', 'CART SAVED | items={n} total={total} added_qty={qty}', [
        'n'     => count($cart),
        'total' => $total,
        'qty'   => $qty,
    ]);
    return redirect()->back()->with('message', 'Product added to cart');
    }

    
    public function updateQuantity(){
$this->trace('updateQuantity', 'ENTER | adjust item quantity');
        
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired ');
    }
    if ($this->hasPendingSale()) {
        return redirect()->to('/newsales')->with('error', 'Complete or cancel the pending payment before changing the cart.');
    }
    
    $product_id = $this->request->getPost('product_id');
    $delta      = $this->request->getPost('delta');
    
    $delta = (int)$delta;
    
    if(!$product_id){
        return redirect()->back()->with('error','product not specified');
    }
    
    $cart = session()->get('cart') ?? [];
    
    $product_model = new ProductModel();
    $product = $product_model->find($product_id);
    
    if(!$product){
        return redirect()->back()->with('error','product not found');
    }
    
    $stock_quantity = (int)$product['stock_quantity'];
    
    $found = false;
    
    foreach($cart as &$item){
        
        if($item['product_id'] == $product_id){
            $found = true;
            
            $new_qty = (int)$item['qty'] + $delta;
            
            if($new_qty < 1){
                $new_qty = 1; 
            }
            
            if($delta > 0 && $new_qty > $stock_quantity){
                $new_qty = $stock_quantity; 
            }
            
            $item['qty'] = $new_qty;
            
            $item['total'] = (float)$item['price'] * $new_qty;
            
            break;
        }
    }
    
    unset($item);
    
    if(!$found){
        $this->trace('updateQuantity', 'ITEM NOT FOUND | product_id={product_id}', ['product_id' => $product_id]);
        return redirect()->back()->with('error','Item not found in cart');
    }
    
    $total = 0;
    foreach($cart as $item){
        $total += (float)$item['total'];
    }
    
    session()->set([
        'cart'  => $cart,
        'total' => $total,
    ]);
    
    $this->trace('updateQuantity', 'CART UPDATED | items={n} total={total}', ['n' => count($cart), 'total' => $total]);
    return redirect()->back()->with('message','Cart updated');
}
    
    public function removeFromCart(){
$this->trace('removeFromCart', 'ENTER | remove item');
    
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }
    if ($this->hasPendingSale()) {
        return redirect()->to('/newsales')->with('error', 'Complete or cancel the pending payment before changing the cart.');
    }
    
    $product_id = $this->request->getPost('product_id');
    if(!$product_id){
        return redirect()->back()->with('error','product not found');
    }
    
    $cart = session()->get('cart') ?? [];
    
    $found = false;
    
    foreach($cart as $index => $item){
        if($item['product_id'] == $product_id){
            
            unset($cart[$index]);
            $found = true;
            
            break;
        }
    }
    
    if(!$found){
        return redirect()->back()->with('error','product cart not found');
    }
    
    $cart = array_values($cart);
    
    $total = 0;
    foreach($cart as $item){
        $total += (float)$item['total'];
    }
    
    session()->set('cart',  $cart);
    session()->set('total', $total);
    
    $this->trace('removeFromCart', 'ITEM REMOVED | items={n} total={total}', ['n' => count($cart), 'total' => $total]);
    return redirect()->to('/newsales')->with('message','Item removed from cart');
    
}
    
    public function clearCart()
    {
$this->trace('clearCart', 'ENTER | clear cart');


    
    $session =session();
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }
    if ($this->hasPendingSale()) {
        return redirect()->to('/newsales')->with('error', 'Complete or cancel the pending payment before clearing the cart.');
    }
   

    
    session()->remove('cart');

    
    session()->remove('total');

    $this->trace('clearCart', 'SESSION CLEARED | cart removed');
    return redirect()->to('/newsales')->with('message','Cart cleared.');

   

    
    }

    public function forceClearPending()
{
$this->trace('forceClearPending', 'ENTER | manual stuck-session reset');
    session()->remove('sale_id');
    session()->remove('transaction_id');
    session()->remove('cart');
    session()->remove('total');
    
    return redirect()->to('/newsales')->with('message', 'Stuck cart and pending sale cleared.');
}


    
    public function checkout()
    {
$this->trace('checkout', 'ENTER | start sale checkout');
    
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }

    $userId = (int) session()->get('user_id');
    if ($userId <= 0) {
        return redirect()->to('/userlogin')->with('error', 'Your user session is invalid. Please log in again.');
    }

    if ($this->hasPendingSale()) {
    error_log('[CHECKOUT-DEBUG] blocked: hasPendingSale() = true. '
        . 'sale_id=' . var_export(session()->get('sale_id'), true)
        . ' total=' . var_export(session()->get('total'), true));
    return redirect()->to('/newsales')->with('error', 'A payment is already pending for this cart.');
}
    
    $cart = session()->get('cart') ?? [];
    
    if(empty($cart)){
        $this->trace('checkout', 'CART EMPTY | abort checkout');
        return redirect()->back()->with('error','selected cart is empty');
    }
    
    $total = 0;
    foreach($cart as $item){
        $total += (float)$item['total'];
    }
    session()->set('total', $total);
    
    $product_model = new ProductModel();
    
    foreach($cart as $item){
        
        $product = $product_model->find($item['product_id']);
        
        if(!$product){
            return redirect()->back()->with('error','product not found');
        }
        
        if((int)$product['stock_quantity'] < (int)$item['qty']){
            return redirect()->back()->with('error','insufficient stock');
        }
        
    }
    $user=session()->get('username');
    
    $db = \Config\Database::connect();
    $db->transStart();
    try {
        $sale_model = new SaleModel();
        
        $sale_data = [
            'status'         => 'PENDING',
            'total_amount'   => $total,
            'sale_date'      => date('Y-m-d'),
            'handled_by_user'=> $user
        ];
        
        $sale_id = $sale_model->insert($sale_data);
        
        if (!$sale_id) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Could not create the sale.');
        }

        $this->trace('checkout', 'SALE INSERTED | sale_id={sale_id} total={total}', [
            'sale_id' => $sale_id,
            'total'   => $total,
        ]);
        
        $sale_model->update($sale_id, ['sale_number' => $sale_id]);
        
        $sale_item_model = new SaleItemModel();

        $User=session()->get('username');
        log_message('debug','session username value:'.var_export($user,true));

        if(!$User){
            log_message('erro','session username not found redirecting back. ');
            return redirect()->back()->with('error','session in username not found');
            
        }
        foreach($cart as $item){
            $sale_item_model->insert([
                'sale_id'             => $sale_id,
                'product_id'          => $item['product_id'],
                'quantity'            => $item['qty'],
                'unit_price'          => $item['price'],
                'subtotal'            => $item['total'],
                'Date'                => date('Y-m-d'),
                'created_by_user' => $User
            ]);
        }
    } catch (\Throwable $e) {
        $db->transRollback();
        session()->remove('sale_id');

      

        

        session()->remove('transaction_id');
        log_message('error', 'Checkout failed before payment modal: {message}', [
            'message' => $e->getMessage(),
        ]);
        return redirect()->to('/newsales')->with('error', 'Checkout could not be created. Please try again.');
    }
    
    $db->transComplete();
    
    if($db->transStatus() === false){
        return redirect()->back()->with('error','checkout transaction failed');
    }

    log_message('debug', 'Checkout committed: sale_id={sale_id}, user_id={user_id}, item_count={item_count}', [
        'sale_id' => $sale_id,
        'user_id' => $user,
        'item_count' => count($cart),
    ]);

    // Set the pending-sale lock only after the sale and all items commit.
    session()->set('sale_id', $sale_id);
    $this->trace('checkout', 'LOCK SET | sale_id={sale_id} -> awaiting payment confirm', ['sale_id' => $sale_id]);
  
    return redirect()->to('/newsales')
        ->with('message','Sale created. Confirm the payment details.')
        ->with('show_payment_modal', true);
}
    
    public function showPaymentPage()
    {
$this->trace('showPaymentPage', 'ENTER | payment page');
    
    $session=session();
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }

    
    $sale_id=session()->get('sale_id');

    
    $total=session()->get('total');

    
    if(!$sale_id){
        return redirect()->to('/newsale')->with('error','sale_id not found in session');
    }

    if(!$total){
        return redirect()->to('/newsale')->with('error','total not found in session');
    }


    
    $sale_model=new SaleModel();

    
    $check_sale_id=$sale_model->find($sale_id);
    if(!$check_sale_id){
        return redirect()->back()->with('error','sale id not found in the Database');
    }
    
    $check_sale_id_status=$sale_model->getSaleStatus($sale_id);
    if(($check_sale_id_status['status'] ?? '') !== 'PENDING'){
        return redirect()->back()->with('message','transaction status error');
    }
    $user=session()->get('username');
    


    
    return view('/newsale',[
        'total'=>$total,
        'user'=>$user,
        'check_sale_id'=>$check_sale_id,
        'check_sale_status'=>$check_sale_id_status

    ]);

    

    
    }

    public function processPayment()
{
$this->trace('processPayment', 'ENTER | payment processing begins');
    // ── ENTRY ──────────────────────────────────────────────────────────────
    error_log('========== PROCESS PAYMENT STARTED ==========');
    error_log('[PAYMENT] REQUEST METHOD: ' . $this->request->getMethod());
    error_log('[PAYMENT] POST data: ' . var_export($this->request->getPost(), true));

    if (!session()->get('logged_in')) {
        error_log('[PAYMENT] EARLY EXIT: not logged in');
        return redirect()->to('/userlogin')->with('error', 'invalid session or session expired');
    }

    $sale_id = session()->get('sale_id');
    $total   = session()->get('total');

    error_log('[PAYMENT] session sale_id=' . var_export($sale_id, true)
        . ' | total=' . var_export($total, true)
        . ' | cart count=' . count(session()->get('cart') ?? []));

    if (!$total) {
        error_log('[PAYMENT] EARLY EXIT: missing sale_id ');
        //return redirect()->to('/newsales')->with('error', 'missing total');
    }else if(!$sale_id){
        error_log('[PAYMENT] EARLY EXIT: missing total in session');
        return redirect()->to('/newsales')->with('error', 'missing sale_id session');

    }

    $sale_model = new SaleModel();
    $current_sale = $sale_model->find($sale_id);
    $current_status = $current_sale['status'] ?? null;
    log_message('debug', 'Payment session check: sale_id={sale_id}, status={status}, total={total}', [
        'sale_id' => $sale_id,
        'status' => $current_status ?? 'missing',
        'total' => $total,
    ]);

    if (!$current_sale) {
        $this->clearPaymentSession(false);
        return redirect()->to('/newsales')->with('error', 'The sale no longer exists. Please start again.');
    }

    if ($current_status === 'PAID') {
        $this->clearPaymentSession(true);
        return redirect()->to('/newsales')->with('message', 'This sale was already paid. A new sale is ready.');
    }

    if ($current_status !== 'PENDING') {
        $this->clearPaymentSession(false);
        return redirect()->to('/newsales')->with('message', 'This payment session is already closed. No duplicate entry.');
    }

    // ── INPUT ──────────────────────────────────────────────────────────────
    $payment_method     = $this->request->getPost('payment_method');
    $transaction_status = $this->request->getPost('transaction_status');
    $gateway_reference  = $this->request->getPost('gateway_reference');

    error_log('[PAYMENT] inputs → method=' . var_export($payment_method, true)
        . ' | status=' . var_export($transaction_status, true)
        . ' | gateway_ref=' . var_export($gateway_reference, true));

    $allowed_methods = ['mobileMoney', 'card', 'cash', 'bank_transfer'];
    if (!in_array($payment_method, $allowed_methods)) {
        error_log('[PAYMENT] EARLY EXIT: invalid payment method');
        return redirect()->to('/newsales')->with('error', 'invalid payment method');
    }

    $allowed_statuses = ['SUCCESS', 'FAILED', 'PENDING', 'CANCELLED'];
    if (!in_array($transaction_status, $allowed_statuses)) {
        error_log('[PAYMENT] EARLY EXIT: invalid transaction status');
        return redirect()->to('/newsales')->with('error', 'invalid transaction status');
    }

    if (empty($gateway_reference)) {
        error_log('[PAYMENT] EARLY EXIT: gateway_reference empty');
        return redirect()->back()->with('error', 'gateway reference was not provided');
    }

    // Use the user-selected transaction_status from the form
    $payment_transaction_model = new PaymentTransaction();

    $transaction_data = [
        'sale_id'            => $sale_id,
        'payment_method'     => $payment_method,
        'transaction_status' => $transaction_status,
        'gateway_reference'  => $gateway_reference,
        'amount_attempted'   => $total,
        'created_at'         => date('Y-m-d H:i:s'),
    ];

    
    try {
        [$transaction_id, $is_new] = $payment_transaction_model->createIdempotent($transaction_data);
        
        if (!$is_new) {
          
            
            error_log('[PAYMENT] IDEMPOTENT: gateway_reference already processed, transaction_id=' . $transaction_id);
            
            $existing_transaction = $payment_transaction_model->find($transaction_id);

            if (!$existing_transaction || (int) $existing_transaction['sale_id'] !== (int) $sale_id) {
                error_log('[PAYMENT] REJECTED: gateway_reference belongs to another sale. '
                    . 'current_sale=' . var_export($sale_id, true)
                    . ' existing_sale=' . var_export($existing_transaction['sale_id'] ?? null, true));

                if ($sale_id) {
                    (new SaleModel())->updateStatus($sale_id, 'CANCELLED');
                }

                session()->remove('sale_id');
                session()->remove('transaction_id');

                return redirect()->to('/newsales')
                    ->with('error', 'This gateway reference was already used for another sale. Please use a new reference.');
            }
            
            // Store in session for consistency with downstream methods
            session()->set('transaction_id', $transaction_id);
            
            // Dispatch based on EXISTING transaction's actual status
            switch ($existing_transaction['transaction_status']) {
                case 'SUCCESS':
                    error_log('[PAYMENT] IDEMPOTENT → calling payment_success()');
                    return $this->completePayment();
                case 'FAILED':
                    error_log('[PAYMENT] IDEMPOTENT → calling payment_failed()');
                    return $this->failPayment();
                case 'PENDING':
                    error_log('[PAYMENT] IDEMPOTENT → calling payment_pending()');
                    return $this->markPaymentPending();
                case 'CANCELLED':
                default:
                    error_log('[PAYMENT] IDEMPOTENT → calling payment_cancelled()');
                    return $this->cancelPayment();
            }
        }
        
        error_log('[PAYMENT] NEW transaction created: ' . $transaction_id);
        
    } catch (\Throwable $e) {
        error_log('[PAYMENT] ERROR in idempotent create: ' . $e->getMessage());
        $this->clearPaymentSession(false);
        return redirect()->to('/newsales')->with('error', 'Payment processing error');
    }

    session()->set('transaction_id', $transaction_id);
    $this->trace('processPayment', 'DISPATCH | transaction_id={txn_id} status={status}', [
        'txn_id' => $transaction_id,
        'status' => $transaction_status,
    ]);
    error_log('[PAYMENT] transaction_id stored in session: ' . $transaction_id);
    error_log('[PAYMENT] switching on status: ' . $transaction_status);

    switch ($transaction_status) {
        case 'SUCCESS':
            error_log('[PAYMENT] → calling payment_success()');
            return $this->completePayment();
        case 'FAILED':
            error_log('[PAYMENT] → calling payment_failed()');
            return $this->failPayment();
        case 'PENDING':
            error_log('[PAYMENT] → calling payment_pending()');
            return $this->markPaymentPending();
        case 'CANCELLED':
        default:
            error_log('[PAYMENT] → calling payment_cancelled() (or default)');
            return $this->cancelPayment();
    }
}
    
    private function completePayment()
    {
$this->trace('completePayment', 'ENTER | payment success');
    

    
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }
    $sale_id        = session()->get('sale_id');
    $transaction_id = session()->get('transaction_id');
    
    $sale_model = new SaleModel();
    
    if(!$sale_id){
        return redirect()->to('/newsales')->with('error','no sale in session');
    }
    $sale = $sale_model->find($sale_id);
    if(!$sale){
        return redirect()->to('/newsales')->with('error','sale not found');
    }
    

    if($sale['status'] === 'PAID'){
        $this->trace('completePayment', 'IDEMPOTENT | sale already PAID - showing receipt only | sale_id={sale_id}', ['sale_id' => $sale_id]);
        error_log('[PAYMENT-SUCCESS] IDEMPOTENT: Sale ' . $sale_id . ' already PAID, skipping processing');
        
       
        session()->remove('sale_id');
        session()->remove('cart');
        session()->remove('total');
        session()->remove('transaction_id');
     
        return redirect()->to('/receipt/view/' . $sale_id)
            ->with('message', 'Payment already processed. Showing receipt.');
    }
    
    $db = \Config\Database::connect();
    $db->transStart();
    
    $payment_model            = new PaymentModel();
    $payment_transaction_model = new PaymentTransaction();
    $userId = session()->get('user_id');
    
    $transaction = $transaction_id
        ? $payment_transaction_model->find($transaction_id)
        : null;

    

    if (!$transaction || $transaction['sale_id'] != $sale_id || $transaction['transaction_status'] !== 'SUCCESS') {
        $db->transRollback();
        error_log('[PAYMENT-SUCCESS] REJECTED: transaction is null, belongs to a '
            . 'different sale, or status is not SUCCESS. Rolling back.');
        $this->clearPaymentSession(false);
        return redirect()->to('/newsales')->with('error', 'A successful payment transaction is required.');
    }
    $payment_method  = $transaction['payment_method'];

    $this->trace('completePayment', 'TRANSACTION VALID | sale_id={sale_id} txn={txn_id} method={method} status=SUCCESS', [
        'sale_id' => $sale_id,
        'txn_id'  => $transaction_id,
        'method'  => $payment_method,
    ]);


    $payment_Status = $transaction['transaction_status'];

   log_message('debug', 'Fetched payment_status: ' . var_export($payment_Status, true));

    
    $total = session()->get('total') ?? 0;
    $payment_model->insert([
        'sale_id'          => $sale_id,
        'payment_method'   => $payment_method,
        'payment_status'       => $payment_Status,                  
        'amount'               => (int) round($total),
        'processed_by_user_id' => $userId,
        'payment_date'         => date('Y-m-d'),
    ]);
    
    $sale_model->updateStatus($sale_id, 'PAID');
    $this->trace('completePayment', 'PAYMENT RECORDED + SALE MARKED PAID | sale_id={sale_id} method={method} status={status}', [
        'sale_id' => $sale_id,
        'method'  => $payment_method,
        'status'  => $payment_Status,
    ]);
    
    if($transaction_id){
        $payment_transaction_model->update($transaction_id, ['transaction_status' => 'SUCCESS']);
    }
    
    $sale_item_model = new SaleItemModel();
    
    $sale_items = $sale_item_model->getItemsBySale($sale_id);
    if(empty($sale_items)){
        $db->transRollback();
        $this->clearPaymentSession(false);
        return redirect()->back()->with('error', 'No sale items found for this sale.');
    }
    
    $product_model = new ProductModel();
    foreach($sale_items as $item){
        $quantity = (int) $item['quantity'];
        
        $updated = $product_model->decrementStock($item['product_id'], $quantity);
        $this->trace('completePayment', 'STOCK DECREMENTED | product={product_id} qty={qty}', [
            'product_id' => $item['product_id'],
            'qty'        => $quantity,
        ]);

        if(!$updated){
            $db->transRollback();
            $this->clearPaymentSession(false);
            return redirect()->back()->with('error', 'Insufficient stock for product '.$item['product_id']);
        }
    }
    
    $db->transComplete();
    
    if($db->transStatus() === false){
        
        $this->clearPaymentSession(false);
        return redirect()->back()->with('error', 'payment transaction failed');
    }
   
    session()->remove('cart');
    session()->remove('total');
    session()->remove('sale_id');
    session()->remove('transaction_id');
    
    $this->trace('completePayment', 'EXIT -> receipt | sale_id={sale_id} session cleared', ['sale_id' => $sale_id]);
    return redirect()->to('/newsales/receipt/'.$sale_id)->with('message','payment success');
    
}
    
    private function failPayment()
{
    $this->trace('failPayment', 'ENTER | payment failed');
    error_log('[PAYMENT-FAILED] entered');

    if (!session()->get('logged_in')) {
        return redirect()->to('/userlogin')->with('error', 'invalid session or session expired');
    }

    $sale_id = session()->get('sale_id');

    if ($sale_id) {
        $sale_model = new SaleModel();
        $payment_transaction_model = new PaymentTransaction();

        $payment_transaction_model->markStatusBySale($sale_id, 'FAILED');
        $sale_model->updateStatus($sale_id, 'FAILED');
        $this->trace('failPayment', 'SALE MARKED FAILED | sale_id={sale_id}', ['sale_id' => $sale_id]);
        error_log('[PAYMENT-FAILED] sale ' . $sale_id . ' marked FAILED');
    }

    
    session()->remove('sale_id');
    session()->remove('transaction_id');
   

    $this->trace('failPayment', 'EXIT -> newsales | lock released, cart kept for retry');
    return redirect()->to('/newsales')->with('error', 'payment failed. please try again');
}
    

    private function markPaymentPending()
{
    $this->trace('markPaymentPending', 'ENTER | payment pending');
    error_log('[PAYMENT-PENDING] entered');

    if (!session()->get('logged_in')) {
        return redirect()->to('/userlogin')->with('error', 'invalid session or session expired');
    }

    $sale_id = session()->get('sale_id');

    if ($sale_id) {
        $sale_model = new SaleModel();
        $payment_transaction_model = new PaymentTransaction();

        $payment_transaction_model->markStatusBySale($sale_id, 'PENDING');
        $sale_model->updateStatus($sale_id, 'PENDING');
        $this->trace('markPaymentPending', 'SALE + TXN MARKED PENDING | sale_id={sale_id}', ['sale_id' => $sale_id]);
    }


    $this->trace('markPaymentPending', 'EXIT -> newsales | lock (sale_id) kept for cron job');
    return redirect()->to('/newsales')->with('message', 'payment is pending. waiting for confirmation');
}


    

    private function cancelPayment()
    {
$this->trace('cancelPayment', 'ENTER | payment cancelled');
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }
    
    $sale_id = session()->get('sale_id');

    
    $sale_model = new SaleModel();

    if(!$sale_id){
        return redirect()->to('/newsales')->with('error','no sale in session');
    }

    $sale = $sale_model->find($sale_id);
    if(!$sale){
        return redirect()->to('/newsales')->with('error','sale not found');
    }

    
    $payment_transaction_model = new PaymentTransaction();
    
    $payment_transaction_model->markStatusBySale($sale_id, 'CANCELLED');
    $this->trace('cancelPayment', 'SALE + TXN MARKED CANCELLED | sale_id={sale_id}', ['sale_id' => $sale_id]);

    
    $sale_model->updateStatus($sale_id, 'CANCELLED');

    
    session()->remove('cart');
    session()->remove('total');
    session()->remove('sale_id');
    session()->remove('transaction_id');

    
    $this->trace('cancelPayment', 'EXIT -> newsales | session cleared');
    return redirect()->to('/newsales')->with('message','payment cancelled');
    }


    
    public function showReceipt($sale_id)
    {
    if(!session()->get('logged_in')){
        return redirect()->to('/userlogin')->with('error','invalid session or session expired');
    }
    
    $sale_model = new SaleModel();
    $this->trace('showReceipt', 'ENTER | sale_id={sale_id}', ['sale_id' => $sale_id]);
    $sale_id = (int)$sale_id;

    
    $sale = $sale_model->find($sale_id);

    
    if(!$sale){
        return redirect()->to('/newsales')->with('error','sale not found');
    }

    
    $sale_item_model = new SaleItemModel();

    
    $sale_items = $sale_item_model->getItemsBySale($sale_id);

    if(empty($sale_items)){
        return redirect()->to('/newsales')->with('error','no sale items found for this sale');
    }

    
    $product_model = new ProductModel();
    $products = [];
    foreach($sale_items as $item){
        $product = $product_model->find($item['product_id']);
        $products[] = [
            'product_name' => $product['product_name'] ?? 'Unknown',
            'price'        => $item['unit_price'],   
            'qty'          => $item['quantity'],     
            'total'        => $item['subtotal'],     
        ];
    }

    
    $payment_model = new PaymentModel();
    
    $payment = $payment_model->getPaymentBySale($sale_id);
    $payment_method = $payment['payment_method'] ?? 'cash';

    
    $user = session()->get('username') ?: 'Unknown';

    
    $total_amount = (float)$sale['total_amount'];

    $receipt_data = [
        'sale_id'          => $sale_id,
        'receipt_number'   => $sale_id,
        'generated_at'     => date('Y-m-d'),
        'handeled_by_user' => $user,
        'total_amount'     => $total_amount,
    ];

    
    $receipt_model = new ReceiptModel();
    
    $existing_receipt = $receipt_model->getBySale($sale_id);
    if($existing_receipt){
        $receipt_data['receipt_number'] = $existing_receipt['receipt_number'];
        $receipt_data['generated_at'] = $existing_receipt['generated_at'];
    } else {
        $receipt_model->insert($receipt_data);
    }

    
    $data = [
        'sale'           => $sale,
        'sale_items'     => $sale_items,
        'products'       => $products,
        'payment_method' => $payment_method,
        'cashier'        => $user,
        'total_amount'   => $total_amount,
        'receipt_number' => $receipt_data['receipt_number'],
        'generated_at'   => $receipt_data['generated_at'],
    ];

    
  

    $this->trace('showReceipt', 'EXIT -> render receipt | sale_id={sale_id} total={total}', [
        'sale_id' => $sale_id,
        'total'   => $total_amount,
    ]);
    return view('Dashboard/test2.html', $data);
    }

    public function listReceipts()
    {
        if(!session()->get('logged_in')){
            return redirect()->to('/userlogin')->with('error','invalid session or session expired');
        }

        
        $receipt_model = new ReceiptModel();
        
        $receipts = $receipt_model->getAllReceipts();

        
        $sale_model = new SaleModel();
        foreach($receipts as &$r){
            $sale = $sale_model->find($r['sale_id'] ?? 0);
            $r['sale_date'] = $sale['sale_date'] ?? $r['generated_at'];
      
      
      
            }
        unset($r);

        $data = [
            'user'     => session()->get('username'),
            'receipts' => $receipts,
        ];

        
        
        
        return redirect()->to(base_url('receipts'));
    }

    private function pendingSaleInfo(): array
    {
        $saleId = session()->get('sale_id');
        if (!$saleId) {
            return ['pending' => false, 'sale_id' => null];
        }

        $sale = (new SaleModel())->getSaleStatus($saleId);

        return [
            'pending' => ($sale !== null && $sale['status'] === 'PENDING'),
            'sale_id' => (int) $saleId,
        ];
    }

    private function hasPendingSale(): bool
    {
        $saleId = session()->get('sale_id');
        if (!$saleId) {
            return false;
        }

        
        $sale = (new SaleModel())->getSaleStatus($saleId);

        error_log('[HASPENDINGSALE] sale_id=' . var_export($saleId, true)
            . ' | status=' . var_export($sale['status'] ?? null, true)
            . ' | returns PENDING=' . (($sale !== null && $sale['status'] === 'PENDING') ? 'YES' : 'no'));
        return $sale !== null && $sale['status'] === 'PENDING';
    }

    private function clearPaymentSession(bool $clearCart): void
    {
        session()->remove('sale_id');
        session()->remove('transaction_id');
        session()->remove('total');

        if ($clearCart) {
            session()->remove('cart');
        }

        log_message('debug', 'Payment session cleared: clear_cart={clear_cart}', [
            'clear_cart' => $clearCart ? 'yes' : 'no',
        ]);
    }

}
