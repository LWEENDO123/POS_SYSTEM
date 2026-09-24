<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sale History — Modernized</title>
  <style>
    /* Layout root */
    .Main {
      display: flex;
      background-color: white;
      min-height: 100vh;
    }

    /* Left sidebar container */
    .box1 {
      min-height: 100vh;
      box-sizing: border-box;
    }

    /* Right main column */
    .box2 {
      display: flex;
      flex-direction: column;
      gap: 40px;
      padding: 24px;
      flex: 1;
      box-sizing: border-box;
    }

    /* Sidebar panel */
    #box1 {
      width: 200px;
      background-color: #0A5741;
      color: #ffffff;
      padding: 20px;
      box-sizing: border-box;
    }

    /* Main content wrapper */
    #box2 {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 24px;
      padding-bottom: 40px;
      box-sizing: border-box;
    }

    /* Sidebar links */
    .nav a {
      display: block;
      text-decoration: none;
      color: white;
      padding-top: 12px;
      padding-bottom: 12px;
      padding-left: 6px;
      border-radius: 8px;
      line-height: 1.2;
      transition: background-color 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .nav a:hover {
      background-color: #f3eded;
      color: #000000;
      transform: scale(1.02);
    }

    /* Top filter / tabs */
    .nav2 {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .topnavbar {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 12px;
      justify-content: flex-start;
      padding-left: 8px;
      box-sizing: border-box;
    }

    .topnavbar nav a {
      display: inline-block;
      text-decoration: none;
      color: #000000;
      background-color: #e6e7ea;
      padding-left: 12px;
      padding-right: 12px;
      padding-top: 6px;
      padding-bottom: 6px;
      border-radius: 20px;
      border: 1px solid transparent;
      box-shadow: 1px 1px 2px rgba(0,0,0,0.08);
      transition: background-color 160ms ease, color 160ms ease;
    }

    .topnavbar nav a:hover {
      background-color: #0A5741;
      color: #ffffff;
    }

    /* Search area */
    #search {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 12px;
      width: 60%;
      max-width: 720px;
      margin-left: 10%;
      padding: 8px;
      border-radius: 20px;
      border: 1px solid #e6e7ea;
      background: #ffffff;
      box-sizing: border-box;
    }

    #search button {
      background-color: #0A5741;
      color: #ffffff;
      padding-left: 12px;
      padding-right: 12px;
      padding-top: 8px;
      padding-bottom: 8px;
      border-radius: 16px;
      border: none;
      cursor: pointer;
    }

    /* Sales table container */
    .saletables {
      border-radius: 16px;
      margin-left: 30px;
      padding: 12px;
      box-shadow: 2px 2px 6px rgba(0,0,0,0.08);
      background-color: #e6e7ea;
      overflow-y: auto;
      max-width: calc(100% - 1px);
      box-sizing: border-box;
      
    }

    /* Table styling */
    .saletables table {
      width: 100%;
      border-collapse: collapse;
      box-sizing: border-box;
    }
    .saletables table h4{
        border: 1px solid;
        border-radius: 20px;
        background-color: #d7d9dd;
        color: #0A5741;
    }
    .h4{
        color: red;
        background-color: white;
    }

    .saletables table th,
    .saletables table td {
      padding: 8px;
      font-family: serif;
      font-size: 14px;
      box-sizing: border-box;
    }

    .saletables table td {
      text-align: center;
      border: 1px solid #d7d9dd;
    }

    .saletables table tr {
      background: #ffffff;
    }

    .saletables table th {
      color: #ffffff;
      background-color: #0A5741;
      padding-top: 10px;
      padding-bottom: 10px;
      border: 1px solid #0A5741;
    }

    .calendar-wrapper{
      
      display: flex;
      flex-direction: column;
      width: 300px;
    }
    .calender{
      line-height: 30px;
      font-family: sans-serif;
      
    }
    
    .months{
      
      display: grid;
      grid-template-columns: repeat(7,3fr);
      gap: 10px;
      padding-top:20px ;
      
      
      
    }
    
    
    .months button{
      border: 1px solid #0A5741;
      border-radius: 2px;
      padding: 1px;
      text-align: center;
      box-shadow: 1px 1px 1px 1px rgb(126, 125, 125);
      
      
    }
    .months button:hover{
      background-color: #0A5741;
      color: white;

    }
    .submit{
      
      margin-top: 30px;
      border-radius: 10px;
      background-color: #0A5741;
      color: whitesmoke;
      height: 30px;
      box-shadow: 1px 1px 1px 1px rgb(126, 125, 125);
      border: 1px solid;
      font-family: sans-serif;
    }
    .submit:hover{
      
      background-color: white;
      color: #000000;
      transition: (7);


    }
    .months label {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
  padding: 1px;
  font-size: 14px;
}


   


    /* Small responsive tweaks */
    @media (max-width: 900px) {
      #box1 {
        width: 180px;
      }

      .month {
        grid-template-columns: repeat(1, 1fr);
        gap: 6px;
      }

      .saletables {
        margin-left: 12px;
        max-width: calc(100% - 220px);
      }
    }

    @media (max-width: 640px) {
      .Main {
        flex-direction: column;
      }

      #box1 {
        width: 100%;
      }

      #box2 {
        padding: 12px;
      }

      

      .months {
        width: 140px;
        height: 36px;
      }

      #search {
        width: 90%;
        margin-left: 5%;
      }
    }
    
  </style>
<?= view('Dashboard/_theme') ?>
  </head>
<body>
  <div class="Main">
    <div id="box1" class="box1">
<?= view('Dashboard/_nav', ['active' => 'sales']) ?>
    </div>

    <div id="box2" class="box2">
      <h4 style="margin:0; color:#0A5741;">SALE HISTORY</h4>

<div id="search">
        <form method="get" action="<?= base_url('sales/search') ?>">
        <input
          type="text"
          name="search"
          placeholder="Search sales, cashier or ID"
          style="flex:1; padding:8px 10px; border-radius:12px; border:1px solid #e6e7ea; outline:none;"
        />
        <button type="submit">Search</button>
        </form>
      </div>

      <?= view('partials/_flash_messages') ?>

      <?php if (!empty($message)): ?>
        <div style="background:#fff3cd; color:#664d03; padding:8px 12px; border-radius:8px;">
          <?= esc($message) ?>
        </div>
      <?php endif; ?>

      <div class="topnavbar">
        <nav class="nav2">
          <a href="<?= base_url('sales/status?status=ALL')  ?>">ALL</a>
          <a href="<?= base_url('sales/status?status=OPEN')  ?>">OPEN</a>
          <a href="<?= base_url('sales/status?status=PAID')  ?>">PAID</a>
          <a href="<?= base_url('sales/status?status=CANCELED')  ?>">CANCELED</a>
          
        </nav>
      </div>

      <div class="saletables" aria-live="polite">
        <table>
          <thead>
            <tr>
              <th >SALE</th>
              <th>DATE</th>
              <th>CASHIER</th>
              <th>ITEMS</th>
              <th>TOTAL</th>
              <th>STATUS</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($Sales)): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#888;">
                  No sales found.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($Sales as $sale): ?>
                <tr>
                  <td><?= esc($sale['sale_id']) ?></td>
                  <td><?= esc($sale['sale_date']) ?></td>
                  <td><?= esc($sale['username'] ?? 'Unknown') ?></td>
                  <td><?= esc($sale['items_count'] ?? $sale['items'] ?? 0) ?></td>
                  <td><?= esc($sale['total_amount']) ?></td>
                  <td><?= esc($sale['status']) ?></td>
                  <td><button style="padding:6px 10px; border-radius:8px; border:none; background:#0A5741; color:#fff; cursor:pointer;">View</button></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  

  <div class="calendar-wrapper">
  <form method="get" action="<?= base_url('sales/searchfilter') ?>" class="calender">

    <h4>Filter</h4>
    <p>Search by day, month and year</p>

    <!-- Month selector -->
    <label>Month</label><br>
    <select name="month">
      <option value="">Select month</option>
      <option value="01">January</option>
      <option value="02">February</option>
      <option value="03">March</option>
      <option value="04">April</option>
      <option value="05">May</option>
      <option value="06">June</option>
      <option value="07">July</option>
      <option value="08">August</option>
      <option value="09">September</option>
      <option value="10">October</option>
      <option value="11">November</option>
      <option value="12">December</option>
    </select><br>

    <!-- Year input -->
    <label>Year</label>
    <input type="text" name="year" placeholder="Enter e.g. 2026">

    <!-- Day grid -->
    <div class="months">
      <label>
      <input type="radio" name="day" value="1">
      <span>1</span>
      </label>
      <label><input type="radio" name="day" value="2"> 2</label>
      <label><input type="radio" name="day" value="3"> 3</label>
      <label><input type="radio" name="day" value="4"> 4</label>
      <label><input type="radio" name="day" value="5"> 5</label>
      <label><input type="radio" name="day" value="6"> 6</label>
      <label><input type="radio" name="day" value="7"> 7</label>
      <label><input type="radio" name="day" value="8"> 8</label>
      <label><input type="radio" name="day" value="9"> 9</label>
      <label><input type="radio" name="day" value="10"> 10</label>
      <label><input type="radio" name="day" value="11"> 11</label>
      <label><input type="radio" name="day" value="12"> 12</label>
      <label><input type="radio" name="day" value="13"> 13</label>
      <label><input type="radio" name="day" value="14"> 14</label>
      <label><input type="radio" name="day" value="15">15</label>
      <label><input type="radio" name="day" value="16"> 16</label>
      <label><input type="radio" name="day" value="17"> 17</label>
      <label><input type="radio" name="day" value="18"> 18</label>
      <label><input type="radio" name="day" value="19"> 19</label>
      <label><input type="radio" name="day" value="20"> 20</label>
      <label><input type="radio" name="day" value="21"> 21</label>
      <label><input type="radio" name="day" value="22"> 22</label>
      <label><input type="radio" name="day" value="23"> 23</label>
      <label><input type="radio" name="day" value="24"> 24</label>
      <label><input type="radio" name="day" value="25"> 25</label>
      <label><input type="radio" name="day" value="26"> 26</label>
      <label><input type="radio" name="day" value="27"> 27</label>
      <label><input type="radio" name="day" value="28"> 28</label>
      <label><input type="radio" name="day" value="29"> 29</label>
      <label><input type="radio" name="day" value="30"> 30</label>
      
      <label><input type="radio" name="day" value="31"> 31</label>
    </div>

    <!-- Optional submit button if user wants to search by month/year only -->
    <button type="submit" class="submit">Submit</button>

  </form>
</div>



  


  

  </div>
</body>


</html>
