<?php
namespace App\Controllers;

use App\Models\SaleModel;

class Sales extends BaseController
{
    
    public function saleshomepage()
    {
        $this->trace('saleshomepage', 'ENTER | sales history page');

        if (!session()->get('logged_in')) {
            $this->trace('saleshomepage', 'AUTH BLOCKED | redirect userlogin');
            return redirect()->to('userlogin')->with('message', 'Session expired.');
        }

        $model = new SaleModel();

        $sales = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username AS username,
                SUM(sale_item.quantity) AS items_count,
                sale.total_amount,
                sale.status
            ')
->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user','user.username = sale.handled_by_user','left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(50)
            ->findAll();

        $this->trace('saleshomepage', 'EXIT -> render Dashboard/sales | count={count}', ['count' => count($sales ?? [])]);
        return view('Dashboard/sales', [
            'Sales' => $sales
        ]);
    }

    
    public function salesstatus()
    {
        $model = new SaleModel();
        $status = strtoupper(trim((string) $this->request->getGet('status')));

        $this->trace('salesstatus', 'ENTER | requested_status={status}', ['status' => $status ?: 'ALL']);

        $builder = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username AS username,
                SUM(sale_item.quantity) AS items_count,
                sale.total_amount,
                sale.status
            ')
            ->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user', 'sale.handled_by_user = user.user_id','left')
            ->groupBy('sale.sale_id')
            ->orderBy('sale.sale_date', 'DESC')
            ->limit(50);

        
        if ($status && $status !== 'ALL') {
            if ($status === 'CANCELED' || $status === 'CANCELLED') {
                $builder->whereIn('sale.status', ['CANCELED', 'CANCELLED']);
            } elseif ($status === 'OPEN') {
                $builder->whereIn('sale.status', ['OPEN', 'PENDING']);
            } else {
                $builder->where('sale.status', $status);
            }
        }
        log_message('debug', $builder->builder()->getCompiledSelect(false));

        $sales = $builder->findAll();
        log_message('debug', 'Sales status result: requested={status}, count={count}, ids={ids}', [
            'status' => $status ?: 'ALL',
            'count' => count($sales),
            'ids' => implode(',', array_column($sales, 'sale_id')),
        ]);

        $this->trace('salesstatus', 'EXIT -> render Dashboard/sales | status={status} count={count}', [
            'status' => $status ?: 'ALL',
            'count'  => count($sales ?? []),
        ]);

        return view('Dashboard/sales', [
            'Sales'  => $sales,
            'status' => $status ?: 'ALL',
            'message' => empty($sales) && $status !== 'ALL'
                ? 'No sales found for status: ' . $status
                : null,
        ]);
    }

    
    public function search()
    {
        $search = $this->request->getGet('search');

        $this->trace('search', 'ENTER | search={search}', ['search' => $search ?: '']);

        $model = new SaleModel();

        $builder = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username AS username,
                SUM(sale_item.quantity) AS items_count,
                sale.total_amount,
                sale.status
            ')
->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user','sale.handled_by_user = user.username','left')
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

        log_message('debug', $builder->builder()->getCompiledSelect(false));

        $results = $builder->findAll();

        if (empty($results)) {
            $this->trace('search', 'EXIT -> render empty list | no matches for search');
            return view('Dashboard/sales', [
                'Sales'   => [],
                'message' => 'No sales found for "' . esc($search) . '".',
            ]);
        }

        $this->trace('search', 'EXIT -> render Dashboard/sales | count={count}', ['count' => count($results ?? [])]);
        return view('Dashboard/sales', [
            'Sales' => $results,
        ]);
    }

   
    public function searchfilter()

    { log_message('debug', 'searchfilter() called');
    $filter_month = $this->request->getGet('month');
    $filter_year  = $this->request->getGet('year');
    $filter_day   = $this->request->getGet('day');
    log_message('debug', "Inputs: day=$filter_day, month=$filter_month, year=$filter_year");
    $this->trace('searchfilter', 'ENTER | day={d} month={m} year={y}', [
        'd' => $filter_day ?: '',
        'm' => $filter_month ?: '',
        'y' => $filter_year ?: '',
    ]);
        $model = new SaleModel();

        $builder = $model
            ->select('
                sale.sale_id,
                sale.sale_date,
                user.username AS username,
                SUM(sale_item.quantity) AS items_count,
                sale.total_amount,
                sale.status
            ')
            ->join('sale_item', 'sale.sale_id = sale_item.sale_id','left')
            ->join('user', 'sale.handled_by_user = user.username','left')
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

        log_message('debug', $builder->builder()->getCompiledSelect(false));

        $results = $builder->findAll();

        if (empty($results)) {
            $this->trace('searchfilter', 'EXIT -> redirect /sales | no records for filter');
            return redirect()->to('/sales')->with('message', 'No records found.');
        }

        $this->trace('searchfilter', 'EXIT -> render Dashboard/sales | count={count}', ['count' => count($results ?? [])]);
        return view('Dashboard/sales', [
            'Sales' => $results,
        ]);
    }
}

