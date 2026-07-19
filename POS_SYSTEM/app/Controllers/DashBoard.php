<?php
// app/Controllers/DashBoard.php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class DashBoard extends BaseController
{   //we first define the function that displays the information defined under index
    public function index()//<-we first define the function
    {  //we use the condition statement to check if the user has a session 
        if (! session()->get('logged_in')) {
            return redirect()->to('userlogin')//<-if no session we rediret to userlogin page
                             ->with('message', 'Session expired or invalid.');//<-we rediret with the message session expired
        }

       //if user has a session we create a variable called user
        $user = [
            'name' => session()->get('username'),//inside user we get the session name
            'role' => session()->get('role')//and also get the role are they cashier,manager,etc
        ];

        
        //on salemodel it defines the table we will work with in this regard it is saleModel
        $saleModel = new SaleModel();//we place the sale model into a variable 

        //we define a variable that carries the salemodel and join tables together
        $recent_sales = $saleModel
            //we use select to show the information we want to see from the different tables 
            ->select("
                sale.sale_id, 
                sale.sale_date,
                sale.total_amount,
                sale.status,

                customer.firstname,
                

                user.username,

                COUNT(sale_item.sale_item_id) AS items_count
            ")//inside the select we select sale.sale_id which connects to the sale table sale as sale_id
              //we select sale_date which connects to the table sale as sale_date
              //we select sale.total_amount which connects to the table sale as total amount
              //we select sale.status which connects to the table sale as status
              //we select customer.firstname which connects to the table customer 
              //we select user.username which connect to the database users
              //the select allows us to display the information getting different parts of the rows 
              //for our case the desired outcome for the select rows will be 
              //sale_id(which is the id product sold)
              //sale_date(the day the product was sold)
              //sale_total amount(the total amount amount)
              //status(is the product open,cancelled or paid)
              //firstname(the customer responsible for the sale)
              //user.username(the cashier name responsible for allowing the sale to go through)
              
            //we use the join to join different types of tables in this case
            ->join(
                'customer',
                'customer.customer_id = sale.customer_id',
                'left'
            )//<- the joint customer.customer_id=sale.customer_id
            //joins the tables customer and sale and uses customer id as the key that links the tables

            //the joint user.user_id=sale.user_id
            ->join(
                'user',
                'user.user_id = sale.user_id',
                'left'
            )//<-joins the tables user and sale and user_id  as the key that links the tables

            //the joint sale_item.sale_id=sale.sale_id
            ->join(
                'sale_item',
                'sale_item.sale_id = sale.sale_id',
                'left'
            )//<-joins the tables sale_item and sale  sale_id  as the key that links the tables
             
            //we use group by to arrange how our information just be displayed by grouping all the rows
            //by sale.sale_id meaning we showing the informatiom about the product our first row starts with sale_id
            ->groupBy('sale.sale_id')
            //orderby allows us to display information based on how we would want to display the sale_id
            //in this case sale.sale_date,DESC tell us that show sale_id by the recent sale_date the date the information was entered
            //and DESC just tell us that show them from descending order
            ->orderBy('sale.sale_date', 'DESC')
            //limit(10) allows us to show a certain amount of information we can use limit to display mybe the first 10 products
            //in our case for limit(10) this tells us from the select pick about 10 rows then cut from there and dont go further

            ->limit(10)
            //findAll is now the query that goes into the database to find the required feilds 
            ->findAll();

        

        // Today's completed sales
        $todaySalesCount = $saleModel//<-todaysalecount is the variable that calls out to the model salemodel

            ->where('sale_date', date('Y-m-d'))//<- by using where to query the sale_date that is recent 
            //and date('y-m-d') as the function which is a comparison which checks todays date and the date currenty
            //available in the sales_date

            ->countAllResults();//<- the countAllResults is a function that counts information found when 
            //the function >where('sale_date', date('Y-m-d')) finds the recent dates how many rows have the recent dates if 5 it will display 5

        // Today's revenue
        $todayRevenue = $saleModel

            ->selectSum('total_amount')//<- in order to find the total revenue we use the function selectsum
            //meaning in my database look for total amount and get the values stored in total amount and add them all
            //meaning if amount for tomato is k35 and organe is k35 the sum function adds 35+35 to produce 70 which is the total


            ->where('sale_date', date('Y-m-d'))//<-after totalamount is calculated we use the where to 
            //to get the recent sale date of the total amount
              
            ->first();//<- we use first when we want to display one result 
            

        // Open Tickets
        $openTickets = $saleModel

            ->where('status', 'OPEN')//<-here we use where to tell us the pending process in our pos system
            //this function checks to find how many rows have there transctions as open

            ->countAllResults();//<- and the count functions counts how many rows they are that got the status open

        

        $productModel = new ProductModel();//<-we call out the product model

        // Total Products
        $productsCount = $productModel->countAllResults();//<-this allows us to count the number of products
        //in the database

        // Low Stock Products
        //low stock allows us to find the products with the lowest stock_quantity in this case stock_quantuty < 10
        $lowStock = $productModel

            ->select("
                product.product_name,
                product.stock_quantity,
                category.category_name AS category
            ")//we select the information we would like displayed about this tables in this case it would be 
            //product name
            //product stock_quantity
            //and category.category_name

            ->join(
                'category',
                'category.category_id = product.category_id',
                'left'
            )// we join the tables category and product using the key category_id as the link between the two tables


            ->where('stock_quantity <', 10)//<-this where function is the condition used that checks for products less than 10

            ->orderBy('stock_quantity', 'ASC')//<- we order the information by stock_quantity in an ascending order 'ASEC'


            ->findAll();//<-fetches the results

        $lowStockCount = count($lowStock);//<-counts the number if products that got low stock less than 10

      

        $categoryModel = new CategoryModel();

        $categoriesCount = $categoryModel->countAllResults();//<-counts the number of categorys currently available

       

        $customerModel = new CustomerModel();

        $customersCount = $customerModel->countAllResults();//<-counts the number of customers currently available

        
        //now comes returning the information to the view so we defind the variable called summery which stores
        //all the queries written and is used to send it to the view  
        $summary = [

            'today_sales' => $todayRevenue['total_amount'] ?? 0,//

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

        ]);//<-we now use  return view('Dashboard/index', to return all information retrived to the view
        

        
    }
    public function newsaledashboard(){
            
    }
}