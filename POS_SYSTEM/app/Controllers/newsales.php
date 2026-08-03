<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use CodeIgniter\Model;

class Newsales extends BaseController
{
    
    public function Newsale()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('userlogin')
                             ->with('message', 'Session expired or invalid.');
        }

        $user = [
            'name' => session()->get('username'),
        ];

        // Load the products
        $product_model = new ProductModel();
        $products = $product_model
            ->select('product.product_id, product.product_name, product.price, product.stock_quantity, category.category_name')
            ->join('category', 'category.category_id = product.category_id')
            ->orderBy('product.product_name', 'ASC')
            ->limit(50)
            ->findAll();

        // Load the customers for the dropdown
        $customer_model = new CustomerModel();
        $get_customers = $customer_model
            ->select('customer_id, firstname')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        // Get the cart from the session
        $cart = session()->get('cart') ?? [];

        return view('DashBoard/newsales', [
            'username'      => $user,
            'product'       => $products,
            'get_customers' => $get_customers,
            'cart'          => $cart,
        ]);
    }

    // Search for a product by name
    public function search_product()
    {
        $search = $this->request->getGet('search');
        $product_model = new ProductModel();

        $builder = $product_model
            ->select('product.product_id, product.product_name, product.price, product.stock_quantity, category.category_name')
            ->join('category', 'category.category_id = product.category_id')
            ->limit(50);

        if (!empty($search)) {
            $builder->like('product.product_name', $search);
        }

        $products = $builder->findAll();

        // Load the customers for the dropdown
        $customer_model = new CustomerModel();
        $get_customers = $customer_model
            ->select('customer_id, firstname')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        // Get the cart from the session
        $cart = session()->get('cart') ?? [];

        return view('DashBoard/newsales', [
            'product'       => $products,
            'get_customers' => $get_customers,
            'cart'          => $cart,
            'search'        => $search,
        ]);
    }

    // Show products, optionally filtered by category
    public function display_products()
    {
        $product_model = new ProductModel();

        $category = $this->request->getGet('category');

        $builder = $product_model
            ->select('product.product_id, product.product_name, product.price, product.stock_quantity, category.category_name')
            ->join('category', 'category.category_id = product.category_id')
            ->orderBy('product.product_name')
            ->limit(50);

        // If the user picked a real category, filter by it.
        // "ALL" or empty means show everything.
        if ($category === 'ALL' || $category === null || $category === '') {
            $products = $builder->findAll();
        } else {
            $builder->where('category.category_name', $category);
            $products = $builder->findAll();
        }

        // Load the customers for the dropdown
        $customer_model = new CustomerModel();
        $get_customers = $customer_model
            ->select('customer_id, firstname')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        // Get the cart from the session
        $cart = session()->get('cart') ?? [];

        return view('DashBoard/newsales', [
            'product'       => $products,
            'get_customers' => $get_customers,
            'cart'          => $cart,
        ]);
    }

    // Change the customer for the current sale
    public function change_customer()
    {
        $session = session();
        $customer_model = new CustomerModel();

        $customer_id = $this->request->getGet('customer_id');

        if (!empty($customer_id)) {
            $customer = $customer_model->find($customer_id);

            if ($customer) {
                $session->set('customer_id', $customer['customer_id']);
                $session->set('customer_name', $customer['firstname']);
            }
        }

        $get_customers = $customer_model
            ->select('customer_id,firstname')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        
        $product_model = new ProductModel();
        $products = $product_model
            ->select('product.product_id, product.product_name, product.price, product.stock_quantity, category.category_name')
            ->join('category', 'category.category_id = product.category_id')
            ->orderBy('product.product_name', 'ASC')
            ->limit(50)
            ->findAll();

        $cart = session()->get('cart') ?? [];

        return view('DashBoard/newsales', [
            'product'            => $products,
            'get_customers'      => $get_customers,
            'cart'               => $cart,
            'selected_customer'  => $session->get('customer_name'),
        ]);
    }

   
    public function cart()
    {
        $session = session();

        $product_id = $this->request->getGet('product_id');
        $category_name = $this->request->getGet('category_name');
        $product_name = $this->request->getGet('product_name');
        $product_price = $this->request->getGet('price');
        $created_by_user_id = session()->get('username');
        $qty = $this->request->getGet('qty') ?? 1;

        $cart = session()->get('cart') ?? [];

        $find_product = false;

       
        foreach ($cart as $index => $item) {
            if ($item['product_id'] == $product_id) {
                $cart[$index]['qty'] += $qty;
                $cart[$index]['total'] = $cart[$index]['qty'] * $cart[$index]['price'];
                $find_product = true;
                break;
            }
        }

        
        if (!$find_product) {
            $total = $qty * $product_price;

            $cart[] = [
                'product_id'      => $product_id,
                'category_name'   => $category_name,
                'product_name'    => $product_name,
                'price'           => $product_price,
                'qty'             => $qty,
                'total'           => $total,
                'created_by_user' => $created_by_user_id,
            ];
        }

        
        $session->set('cart', $cart);

        
        return redirect()->to('/newsales');
    }

    
    public function update_qty()
    {
        $session = session();
        $cart = session()->get('cart') ?? [];

        $product_id = $this->request->getPost('product_id');
        $delta = (int) $this->request->getPost('delta');

        foreach ($cart as $index => $item) {
            if ($item['product_id'] == $product_id) {
                $cart[$index]['qty'] += $delta;

                
                if ($cart[$index]['qty'] < 1) {
                    $cart[$index]['qty'] = 1;
                }

                $cart[$index]['total'] = $cart[$index]['qty'] * $cart[$index]['price'];
                break;
            }
        }

        $session->set('cart', $cart);

        return redirect()->to('/newsales');
    }

    
    public function view()
    {
        $session = session();
        $cart = $session->get('cart') ?? [];

        return view('DashBoard/newsales', [
            'cart' => $cart,
        ]);
    }

    
    public function remove($product_id = null)
    {
        $session = session();
        $cart = session()->get('cart') ?? [];

        
        if ($product_id === null) {
            $product_id = $this->request->getPost('product_id');
        }

        foreach ($cart as $index => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart[$index]);
                break;
            }
        }

        
        $cart = array_values($cart);

        $session->set('cart', $cart);

        return redirect()->to('/newsales');
    }

   
    public function clear()
    {
        $session = session();
        $session->remove('cart');

        return redirect()->to('/newsales');
    }

   
    public function checkout()
    {
        $session = session();
        $cart = $session->get('cart') ?? [];
        $created_by_user_name = $session->get('username');

        if (empty($cart)) {
            return redirect()->to('/newsales')->with('message', 'Cart is empty.');
        }

        $cartmodel = new CartModel();

        foreach ($cart as $item) {
            $cartmodel->insert([
                'product_id'         => $item['product_id'],
                'category_name'      => $item['category_name'],
                'product_name'       => $item['product_name'],
                'price'              => $item['price'],
                'qty'                => $item['qty'],
                'total'              => $item['total'],
                'created_by_user_name' => $created_by_user_name,
            ]);
        }

       
        $session->remove('cart');

        return redirect()->to('/newsales')->with('message', 'Checkout successful!');
    }
}
