<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ReceiptModel;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\PaymentModel;

/*
============================================================
RECEIPT CONTROLLER
PURPOSE:
Serve the Receipts page (app/Views/Dashboard/receipt.php).

1. index()  - list receipts (newest first.
2. search()   - filter receipts by receipt_number (partial match.
              Also falls back to listing all when empty.

The view expects each row to have these keys:
- receipt_num    (from receipt_number)
- sale_id
- total          (from total_amount)
- cashier_name   (from handeled_by_user)
- date           (from generated_at)
============================================================
*/

class Receipt extends BaseController
{
    /*
    ENDPOINT: Receipt::index (GET /receipts)
    PURPOSE: display all receipts in a table.
    */
    public function index()
    {
        $this->trace('index', 'ENTER | receipts list page');

        if(!session()->get('logged_in')){
            $this->trace('index', 'AUTH BLOCKED | redirect userlogin');
            return redirect()->to('/userlogin')->with('error','invalid session or session expired');
        }

        $receipt_model = new ReceiptModel();
        // FIX: query logic moved to the MODEL (ReceiptModel::getAllReceipts).
        $receipts = $receipt_model->getAllReceipts(50);

        $this->trace('index', 'EXIT -> render Dashboard/receipt | count={count}', ['count' => count($receipts ?? [])]);

        return view('Dashboard/receipt', [
            'receipts' => $this->mapRows($receipts),
        ]);
    }

    /*
    ENDPOINT: Receipt::search (GET /receipts/search?search=XXX)
    PURPOSE: search receipts by receipt number (partial match).
    Shows ALL receipts when the search box is empty.

    FIX (vs the old broken script):
    1. The old code ran at the top level with $this->request - now
       it is a proper method.
    2. getGet('search') returns ONE value (not the whole array); no
       need to cast to int - receipt numbers are strings like SL-...
    3. 'search_number' was not a column; the real column is
       'receipt_number'. Now uses ->like('receipt_number', ...).
    */
    public function search()
    {
        $this->trace('search', 'ENTER | receipt search');

        if(!session()->get('logged_in')){
            $this->trace('search', 'AUTH BLOCKED | redirect userlogin');
            return redirect()->to('/userlogin')->with('error','invalid session or session expired');
        }

        $search = $this->request->getGet('search');
        
        $receipt_model = new ReceiptModel();

        // FIX: query logic moved to the MODEL (ReceiptModel::searchByNumber).
        if(!empty($search)){
            $receipts = $receipt_model->searchByNumber($search, 50);
        } else {
            $receipts = $receipt_model->getAllReceipts(50);
        }

        $this->trace('search', 'EXIT -> render Dashboard/receipt | search={search} count={count}', [
            'search' => $search ?: '',
            'count'  => count($receipts ?? []),
        ]);

        return view('Dashboard/receipt', [
            'receipts' => $this->mapRows($receipts),
            'search'   => $search,
        ]);
    }

    /*
    Map DB columns to the keys the view uses:
    receipt_number   -> receipt_num
    total_amount     -> total
    handeled_by_user -> cashier_name
    generated_at      -> date (formatted d/m/Y)
    */
    private function mapRows(array $rows): array
    {
        $mapped = [];
        foreach($rows as $r){
            $mapped[] = [
                'receipt_num'  => $r['receipt_number'] ?? '',
                'sale_id'      => $r['sale_id'] ?? '',
                'total'         => $r['total_amount'] ?? 0,
                'cashier_name'  => $r['handeled_by_user'] ?? '—',
                'date'          => !empty($r['generated_at'])
                                    ? date('d/m/Y', strtotime($r['generated_at']))
                                    : '',
            ];
        }
        return $mapped;
    }

    /*
    ENDPOINT: Receipt::view_receipt (GET /receipts/view/{sale_id})
    PURPOSE:
    Show the RECEIPT PREVIEW (Dashboard/test2.html) for one sale.
    This is what the "View" button on each receipts-table row opens.

    FIX (vs the old view_receipt() that lived in ReceiptModel):
    1. A MODEL must never return a view() or use $this->response -
       models are data-only. Returning views is the CONTROLLER's job.
    2. The old version lived in the model AND the route pointed at the
       model (\App\Models\ReceiptModel::view_receipt) - routes must
       point at CONTROLLERS.
    3. The DB queries still live in the MODELS (getBySale,
       getItemsBySaleWithProducts, getPaymentBySale) per our MVC rule.
    */
    public function view_receipt($sale_id = null)
    {
        $this->trace('view_receipt', 'ENTER | sale_id={sale_id}', ['sale_id' => $sale_id ?: '(from request)']);

        if(!session()->get('logged_in')){
            $this->trace('view_receipt', 'AUTH BLOCKED | redirect userlogin');
            return redirect()->to('/userlogin')->with('error','invalid session or session expired');
        }

        $receipt_model = new ReceiptModel();

        /*
        Accept the sale id from:
        1. the URL segment:  /receipts/view/93      (the View buttons use this)
        2. POST sale_id      (compat with the old POST route)
        3. POST receipt_id   (old route compat - look up its sale_id)
        */
        if(empty($sale_id)){
            $sale_id    = $this->request->getPost('sale_id');
            $receipt_id = $this->request->getPost('receipt_id');

            if(empty($sale_id) && !empty($receipt_id)){
                $by_id = is_numeric($receipt_id)
                    ? $receipt_model->find($receipt_id)
                    : $receipt_model->where('receipt_number', $receipt_id)->first();
                $sale_id = $by_id['sale_id'] ?? null;
            }
        }

        if(empty($sale_id)){
            $this->trace('view_receipt', 'NO SALE SELECTED | redirect /receipts');
            return redirect()->to('/receipts')->with('error','No sale selected for this receipt.');
        }

        $sale_id = (int)$sale_id;

        // All DB work happens in the models.
        $sale_model      = new SaleModel();
        $sale_item_model = new SaleItemModel();
        $payment_model   = new PaymentModel();

        $sale = $sale_model->find($sale_id);
        if(!$sale){
            $this->trace('view_receipt', 'SALE NOT FOUND | sale_id={sale_id}', ['sale_id' => $sale_id]);
            return redirect()->to('/receipts')->with('error','Sale not found.');
        }

        $receipt = $receipt_model->getBySale($sale_id);

        /*
        If this sale has no receipt row yet (older sales), create one now
        so it also appears in the receipts list table.
        */
        if(!$receipt){
            $this->trace('view_receipt', 'RECEIPT NOT FOUND | creating one for sale_id={sale_id}', ['sale_id' => $sale_id]);
            $receipt_model->insert([
                'sale_id'          => $sale_id,
                'receipt_number'   => 'SL-' . date('Ymd') . '-' . $sale_id,
                'generated_at'     => date('Y-m-d H:i:s'),
                'handeled_by_user' => session()->get('username') ?: 'Unknown',
                'total_amount'     => $sale['total_amount'] ?? 0,
            ]);
            $receipt = $receipt_model->getBySale($sale_id);
        }

        // Items WITH product names - the product JOIN lives in SaleItemModel.
        $items    = $sale_item_model->getItemsBySaleWithProducts($sale_id);
        $products = [];
        foreach($items as $i){
            $products[] = [
                'product_name' => $i['product_name'] ?? 'Unknown Item',
                'qty'          => $i['quantity'],   // DB column (not the cart's 'qty' key)
                'total'        => $i['subtotal'],   // DB column (not the cart's 'total' key)
            ];
        }

        $payment = $payment_model->getPaymentBySale($sale_id);

        // These keys match Dashboard/test2.html exactly.
        $data = [
            'receipt_number' => $receipt['receipt_number']   ?? 'N/A',
            'generated_at'   => $receipt['generated_at']     ?? '',
            'cashier'        => $receipt['handeled_by_user'] ?? session()->get('username'),
            'sale'           => $sale,
            'products'       => $products,
            'total_amount'   => $receipt['total_amount'] ?? ($sale['total_amount'] ?? 0),
            'payment_method' => $payment['payment_method'] ?? 'N/A',
        ];

        // POPUP MODE: the Receipts list and the Dashboard fetch this endpoint
        // with ?modal=1 (or an AJAX header) and inject the returned fragment
        // into an on-page modal - the user stays on the same page.
        if($this->request->getGet('modal') === '1' || $this->request->isAJAX()){
            $this->trace('view_receipt', 'EXIT -> render _receipt_card (modal fragment) | sale_id={sale_id}', ['sale_id' => $sale_id]);
            return view('Dashboard/_receipt_card', $data);
        }

        // Full page (direct visits / old bookmarks).
        $this->trace('view_receipt', 'EXIT -> render test2.html (full page) | sale_id={sale_id}', ['sale_id' => $sale_id]);
        return view('Dashboard/test2.html', $data);
    }
}