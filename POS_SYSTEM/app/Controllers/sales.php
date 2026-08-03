<?php
namespace App\Controllers;

use App\Models\SaleModel;

class Sales extends BaseController
{
    
    public function saleshomepage()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('userlogin')->with('message', 'Session expired.');
        }

        $model = new SaleModel();

        $sales = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username,
                COUNT(sale_item.sale_item_id) AS items,
                sale.total_amount,
                sale.status
            ')
            ->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user', 'sale.user_id = user.user_id','left')
            ->join('customer', 'customer.customer_id = sale.customer_id','left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(50)
            ->findAll();

        return view('Dashboard/sales', [
            'Sales' => $sales
        ]);
    }

    
    public function salesstatus()
    {
        $model = new SaleModel();
        $status = $this->request->getGet('status');

        $builder = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username,
                COUNT(sale_item.sale_item_id) AS items,
                sale.total_amount,
                sale.status
            ')
            ->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user', 'sale.user_id = user.user_id','left')
            ->join('customer', 'customer.customer_id = sale.customer_id','left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(50);

        
        if ($status && $status !== 'ALL') {
            $builder->where('sale.status', $status);
        }

        $sales = $builder->findAll();

        return view('Dashboard/sales', [
            'Sales' => $sales
        ]);
    }

    
    public function search()
    {
        $search = $this->request->getGet('search');
        $model = new SaleModel();

        $builder = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username,
                COUNT(sale_item.sale_item_id) AS items,
                sale.total_amount,
                sale.status
            ')
            ->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user', 'sale.user_id = user.user_id','left')
            ->join('customer', 'customer.customer_id = sale.customer_id','left')
            ->join('product', 'product.product_id = sale_item.product_id','left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(50);

        // Only search when the user typed something
        if (!empty($search)) {
            $builder->like('product.product_name', $search);
            $builder->orLike('user.username', $search);
            $builder->orLike('sale.sale_id', $search);
        }

        $results = $builder->findAll();

        if (empty($results)) {
            return view('Dashboard/sales', [
                'Sales'   => [],
                'message' => 'No sales found for "' . esc($search) . '".',
            ]);
        }

        return view('Dashboard/sales', [
            'Sales' => $results,
        ]);
    }

   
    public function searchfilter()
    {
        $filter_month = $this->request->getGet('month');
        $filter_year  = $this->request->getGet('year');
        $filter_day   = $this->request->getGet('day');

        $model = new SaleModel();

        $builder = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username,
                COUNT(sale_item.sale_item_id) AS items,
                sale.total_amount,
                sale.status
            ')
            ->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user', 'sale.user_id = user.user_id','left')
            ->join('customer', 'customer.customer_id = sale.customer_id','left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(50);

        
        if (!empty($filter_day) && !empty($filter_month) && !empty($filter_year)) {
            $date = $filter_year . '-' . $filter_month . '-' . $filter_day;
            $builder->where('sale.sale_date', $date);

        } elseif (!empty($filter_month) && !empty($filter_year)) {
            $builder->like('sale.sale_date', $filter_year . '-' . $filter_month);
            
        } elseif (!empty($filter_year)) {
            $builder->like('sale.sale_date', $filter_year);
        }

        $results = $builder->findAll();

        if (empty($results)) {
            return redirect()->to('/sales')->with('message', 'No records found.');
        }

        return view('Dashboard/sales', [
            'Sales' => $results,
        ]);
    }
}

