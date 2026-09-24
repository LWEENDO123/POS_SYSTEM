<?php
// app/Controllers/DashBoard.php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;


class DashBoard extends BaseController
{
    public function index()
    {
        $this->trace('index', 'ENTER | dashboard');

        if (! session()->get('logged_in')) {
            $this->trace('index', 'AUTH BLOCKED | no session -> redirect userlogin');
            return redirect()->to('userlogin')
                             ->with('message', 'Session expired or invalid.');
        }

        $user = [
            'name' => session()->get('username'),
            'role' => session()->get('role')
        ];

        $saleModel = new SaleModel();

        $recent_sales= $saleModel->getSales(10);

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

        $summary = [
            'today_sales' => $todayRevenue['total_amount'] ?? 0,
            'todays_sales_count' => $todaySalesCount,
            'open_tickets' => $openTickets,
            'low_stock_count' => $lowStockCount,
            'products_count' => $productsCount,
            'categories_count' => $categoriesCount
        ];
        $session=session();
        $User=session()->get('username');

        $this->trace('index', 'EXIT -> render Dashboard/index | today_sales={s} open={o} products={p}', [
            's' => $summary['today_sales'],
            'o' => $summary['open_tickets'],
            'p' => $summary['products_count'],
        ]);

        return view('Dashboard/index', [
            'user' => $User,
            'summary' => $summary,
            'recent_sales' => $recent_sales,
            'low_stock' => $lowStock
        ]);
    }

    
}
