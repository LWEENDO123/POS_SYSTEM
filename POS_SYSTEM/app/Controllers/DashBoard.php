<?php
// app/Controllers/DashBoard.php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;


class DashBoard extends BaseController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('userlogin')
                             ->with('message', 'Session expired or invalid.');
        }

        $user = [
            'name' => session()->get('username'),
            'role' => session()->get('role')
        ];

        $saleModel = new SaleModel();

        $recent_sales = $saleModel
            ->select("
                sale.sale_id, 
                sale.sale_date,
                sale.total_amount,
                sale.status,
                customer.firstname,
                user.username,
                COUNT(sale_item.sale_item_id) AS items_count
            ")
            ->join('customer', 'customer.customer_id = sale.customer_id', 'left')
            ->join('user', 'user.user_id = sale.user_id', 'left')
            ->join('sale_item', 'sale_item.sale_id = sale.sale_id', 'left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(10)
            ->findAll();

        $todaySalesCount = $saleModel
            ->where('sale_date', date('Y-m-d'))
            ->countAllResults();

        $todayRevenue = $saleModel
            ->selectSum('total_amount')
            ->where('sale_date', date('Y-m-d'))
            ->first();

        $openTickets = $saleModel
            ->where('status', 'OPEN')
            ->countAllResults();

        $productModel = new ProductModel();
        $productsCount = $productModel->countAllResults();

        $lowStock = $productModel
            ->select("
                product.product_name,
                product.stock_quantity,
                category.category_name AS category
            ")
            ->join('category', 'category.category_id = product.category_id', 'left')
            ->where('stock_quantity <', 10)
            ->orderBy('stock_quantity', 'ASC')
            ->findAll();

        $lowStockCount = count($lowStock);

        $categoryModel = new CategoryModel();
        $categoriesCount = $categoryModel->countAllResults();

        $customerModel = new CustomerModel();
        $customersCount = $customerModel->countAllResults();

        $summary = [
            'today_sales' => $todayRevenue['total_amount'] ?? 0,
            'todays_sales_count' => $todaySalesCount,
            'open_tickets' => $openTickets,
            'low_stock_count' => $lowStockCount,
            'products_count' => $productsCount,
            'categories_count' => $categoriesCount,
            'customers_count' => $customersCount
        ];

        return view('Dashboard/index', [
            'user' => $user,
            'summary' => $summary,
            'recent_sales' => $recent_sales,
            'low_stock' => $lowStock
        ]);
    }

    public function sales(){
        if (! session()->get('logged_in')) {
            return redirect()->to('userlogin')
                             ->with('message', 'Session expired or invalid.');
        }
        return view('DashBoard/sales');
    }

    public function newsaledashboard(){
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->select('category_name')->findAll();

        $searchTerm = $this->request->getGet('search');

        if ($searchTerm) {
            $products = $productModel
                ->select('
                    product.product_id,
                    product.product_name,
                    product.price,
                    category.category_name AS category
                ')
                ->join('category', 'product.category_id = category.category_id', 'left')
                ->like('product.product_name', $searchTerm)
                ->findAll();
        } else {
            $products = $productModel
                ->select('
                    product.product_id,
                    product.product_name,
                    product.price,
                    category.category_name AS category
                ')
                ->join('category', 'product.category_id = category.category_id', 'left')
                ->findAll();
        }

        // Get cart from session
        $session = session();
        $cart = $session->get('cart') ?? [];

        $total_items  = 0;
        $total_amount = 0;
        foreach ($cart as $item) {
            $qty = $item['qty'] ?? 1;
            $total_items  += $qty;
            $total_amount += $qty * ($item['price'] ?? 0);
        }

        return view('DashBoard/sales', [
            'category' => $category,
            'products' => $products,
            'cart' => $cart,
            'total_items' => $total_items,
            'total_amount' => $total_amount,
        ]);
    }

    public function add_to_cart() {
        $session = session();

        $product_id   = $this->request->getPost('product_id');
        $product_name = $this->request->getPost('product_name');
        $price        = $this->request->getPost('price');
        $qty          = $this->request->getPost('qty') ?: 1;

        $cart = $session->get('cart') ?? [];

        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['qty'] += $qty;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $cart[] = [
                'product_id'   => $product_id,
                'product_name' => $product_name,
                'price'        => $price,
                'qty'          => (int)$qty
            ];
        }

        $session->set('cart', $cart);

        // Redirect back to sales page
        return redirect()->to('DashBoard/sales')->with('message', 'Item added to cart');
    }

    public function checkout() {
        $session = session();
        $cart    = $session->get('cart') ?? [];

        if (empty($cart)) {
            return redirect()->to('DashBoard/sales')->with('error', 'Cart is empty');
        }

        // Get logged-in user
        $userId = session()->get('user_id');

        $saleModel = new SaleModel();

        $total_amount = 0;
        foreach ($cart as $item) {
            $total_amount += ($item['qty'] ?? 1) * ($item['price'] ?? 0);
        }

        $sale_id = $saleModel->insert([
            'user_id'      => $userId,
            'sale_date'    => date('Y-m-d H:i:s'),
            'status'       => 'PAID',
            'total_amount' => $total_amount,
        ]);

        $saleItemModel = new SaleItemModel();
        foreach ($cart as $item) {
            $qty    = $item['qty'] ?? 1;
            $price  = $item['price'] ?? 0;
            $saleItemModel->insert([
                'sale_id'            => $sale_id,
                'product_id'         => $item['product_id'],
                'quantity'           => $qty,
                'unit_price'         => $price,
                'subtotal'           => $qty * $price,
                'created_by_user_id' => $userId,
            ]);
        }

        // Clear cart
        $session->remove('cart');

        return redirect()->to('DashBoard/index')->with('message', 'Sale completed!');
    }

    public function clear_cart() {
        $session = session();
        $session->remove('cart');
        return redirect()->to('DashBoard/sales')->with('message', 'Cart cleared');
    }
}
