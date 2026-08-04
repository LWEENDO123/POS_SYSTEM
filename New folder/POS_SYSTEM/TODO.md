# TODO — Connect & clean up the Newsale + Sales system

## app/Controllers/newsales.php
- [x] `Newsale()` now loads products, customers, and cart and passes them all to the view
- [x] `search_product()` passes `product`, `get_customers`, `cart`, and `search`
- [x] `display_products()` fixed the `ALL` category check and passes all needed data
- [x] `change_customer()` passes products, customers, cart, and selected customer
- [x] `cart()` fixed by looping by index so quantity updates actually save
- [x] Added `update_qty()` method (used by the +/- buttons in the view)
- [x] `remove()` accepts a product_id from the route or the form
- [x] All redirects changed from `/cart/view` to `/newsales`
- [x] `checkout()` uses correct CartModel field names (`price`, `qty`, `total`)

## app/Views/Dashboard/newsales.php
- [x] Fixed the "Personal Care" link (now uses `%20` for the space)
- [x] Fixed the "Cashier" link (now points to `/newsales` instead of a non-existent route)
- [x] Customer dropdown uses `firstname` (matches CustomerModel)

## app/Controllers/sales.php
- [x] `saleshomepage()` loads the latest 50 sales and passes them as `Sales`
- [x] `salesstatus()` filters by status (ALL/OPEN/PAID/CANCELED) and passes `Sales`
- [x] `search()` searches by product name, cashier name, or sale id and passes `Sales`
- [x] `searchfilter()` filters by day/month/year and passes `Sales`
- [x] Removed the broken `session()->start()` line and undefined `$build_query`/`$result` variables
- [x] All return views use the correct path `Dashboard/sales` and the `Sales` key

## app/Views/Dashboard/sales.php
- [x] Search form now points to `sales/search`
- [x] Status filter links now point to `sales/status`
- [x] Filter form now points to `sales/searchfilter`
- [x] Sidebar links now point to real routes (Dashboard, New Sale, Sale, Products, Customers, Cashiers)
- [x] Added a flash message display for "No records found" etc.

## app/Config/Routes.php
- [x] Removed the `newsales/product_category` route (method no longer exists)
- [x] Added sales routes: `sales`, `sales/status`, `sales/search`, `sales/searchfilter`
- [x] Routes now match the controller methods

## Verification
- [x] `php -l` passes on all changed files
