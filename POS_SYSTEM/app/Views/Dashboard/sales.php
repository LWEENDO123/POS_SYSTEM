<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tillkeep POS — Sales</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #ECEFEA;
    }

    .container {
      display: flex;
      min-height: 100vh;
      border: 2px solid rgb(103, 18, 173);
    }

    /* Sidebar */
    .sidebar {
      border: 1px solid yellow;
      width: 200px;
      background-color: #0A5741;
      color: white;
    }
    .information nav ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .information nav ul li {
      padding: 15px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      cursor: pointer;
    }
    .information nav ul li:hover {
      background-color: #a0522d;
    }

    /* Main content */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
      border: 2px solid rgb(15, 168, 168);
      padding: 20px;
    }

    /* Search bar + options */
    .searchbar {
      width: 100%;
      border: 1px solid #ccc;
      padding: 20px;
      margin-bottom: 20px;
      background-color: #fff;
      border-radius: 10px;
    }
    .search {
      border: 1px solid #333;
      border-radius: 20px;
      padding: 8px;
      margin-bottom: 20px;
    }
    .options {
      display: flex;
      justify-content: center;
      gap: 30px;
    }
    .options a {
      padding: 5px 10px;
      border: 1px solid #333;
      border-radius: 5px;
      text-decoration: none;
      color: #333;
    }
    .options a:hover {
      background-color: #0A5741;
      color: white;
    }

    /* Product grid */
    .boxes {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      border: 4px solid rgb(122, 11, 11);
      padding: 10px;
    }
    .boxe1 {
      border: 1px solid red;
      border-radius: 10px;
      background-color: #fff;
      height: 100px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }
    .boxe1:hover {
      background-color: #f4f4f4;
      transform: scale(1.05);
    }

    /* Receipt section */
    .recept {
      flex: 1;
      margin-left: 20px;
    }
    .receptcard {
      display: flex;
      flex-direction: column;
      align-items: center;
      border: 4px solid yellow;
      padding: 20px;
      background-color: #fff;
      border-radius: 10px;
    }
    .recitetable {
      width: 100%;
      border: 1px solid green;
      margin-bottom: 20px;
      border-radius: 10px;
      padding: 10px;
    }
    .recitetable h4 {
      text-align: center;
      margin: 0;
    }
    .recitetable table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .recitetable th, .recitetable td {
      border: 1px solid #ccc;
      padding: 6px;
      text-align: center;
    }
    .recitetable th {
      background-color: #0A5741;
      color: white;
    }

    /* Buttons */
    .chargebutton, .clearbutton {
      width: 210px;
      height: 30px;
      border: 2px solid #333;
      border-radius: 20px;
      text-align: center;
      line-height: 30px;
      background-color: whitesmoke;
      margin-bottom: 10px;
      cursor: pointer;
    }
    .chargebutton:hover, .clearbutton:hover {
      background-color: green;
      color: white;
      box-shadow: 2px 2px 5px #333;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="information">
        <nav>
          <ul>
            <li>Dashboard</li>
            <li>New Sale</li>
            <li>Sales</li>
            <li>Products</li>
            <li>Customers</li>
            <li>Cashiers</li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Main content -->
    <div class="main">
      <!-- Search bar -->
      <div class="searchbar">
        <div class="search">Search</div>
        <div class="options">
          <nav>
            <a href="#">All</a>
            <a href="#">Beverage</a>
            <a href="#">Snacks</a>
            <a href="#">Bakery</a>
            <a href="#">Personal Care</a>
          </nav>
        </div>
      </div>

      <!-- Product grid -->
      <div class="boxcontaner">
        <div class="boxes">
          <!-- Loop products from backend -->
          <div class="boxe1">Product 1</div>
          <div class="boxe1">Product 2</div>
          <div class="boxe1">Product 3</div>
          <div class="boxe1">Product 4</div>
          <!-- more boxes -->
        </div>
      </div>
    </div>

    <!-- Receipt section -->
    <div class="recept">
      <div class="receptcard">
        <div class="recitetable">
          <h4>Tillkeep · Sale #NEW</h4>
          <p>Jul 16, 07:16 AM — Mwansa Tembo</p>
          <button>Change</button>
          <table>
            <thead>
              <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
              </tr>
            </thead>
            <tbody>
              <!-- Loop sale items from backend -->
              <tr>
                <td>Chocolate Cookies 100g</td>
                <td>
                  <button>+</button> 1 <button>-</button>
                </td>
                <td>K520</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="chargebutton">Charge Customer</div>
        <div class="clearbutton">Clear Sale</div>
      </div>
    </div>
  </div>
</body>
</html>
