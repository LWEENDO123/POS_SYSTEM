<?php
// app/Controllers/DashBoard.php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
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

            ->join(
                'customer',
                'customer.customer_id = sale.customer_id',
                'left'
            )

            ->join(
                'user',
                'user.user_id = sale.user_id',
                'left'
            )

            ->join(
                'sale_item',
                'sale_item.sale_id = sale.sale_id',
                'left'
            )

            ->groupBy('sale.sale_id')

            ->orderBy('sale.sale_date', 'DESC')

            ->limit(10)

            ->findAll();

        

        // Today's completed sales
        $todaySalesCount = $saleModel

            ->where('sale_date', date('Y-m-d'))

            ->countAllResults();

        // Today's revenue
        $todayRevenue = $saleModel

            ->selectSum('total_amount')

            ->where('sale_date', date('Y-m-d'))

            ->first();

        // Open Tickets
        $openTickets = $saleModel

            ->where('status', 'OPEN')

            ->countAllResults();

        

        $productModel = new ProductModel();

        // Total Products
        $productsCount = $productModel->countAllResults();

        // Low Stock Products
        $lowStock = $productModel

            ->select("
                product.product_name,
                product.stock_quantity,
                category.category_name AS category
            ")

            ->join(
                'category',
                'category.category_id = product.category_id',
                'left'
            )

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
    public function newsaledashboard(){
            
    }
}