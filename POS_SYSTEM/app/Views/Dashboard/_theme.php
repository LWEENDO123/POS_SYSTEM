<?php
/*
SHARED VISUAL THEME — Dashboard design reference
=================================================
The Dashboard look: fixed green sidebar (220px), light canvas, white cards,
green-header tables, rounded buttons. Included in the <head> of every page
AFTER that page's own CSS so it overrides the look while the page markup,
PHP logic and JS all stay untouched.
*/
?>
<style>
  :root{
    --accent:#0A5741;
    --muted:#ECEFEA;
    --card:#ffffff;
    --danger:#AE3E37;
    --shadow:rgba(0,0,0,0.08);
    --radius:10px;
  }
  *{ box-sizing:border-box; }
  body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:var(--muted);
    color:#222;
  }

  /* ---------- NEW SALE page (.container grid -> dashboard shell) ---------- */
  body .container{
    display:flex;
    flex-direction:row;
    flex-wrap:wrap;
    gap:0;
    background:var(--muted);
    padding:0;
    min-height:100vh;
  }
  body .container > header{
    width:100%;
    background:var(--card);
    border-bottom:1px solid #e2e8f0;
    padding:14px 24px;
  }
  body .container > header .links a,
  body .container .links a{
    display:inline-block; text-decoration:none; color:var(--accent);
    border:1px solid var(--accent); border-radius:20px; padding:6px 14px;
    margin-right:8px; font-size:13px; background:#fff;
  }
  body .container .links a:hover{ background:var(--accent); color:#fff; }

  body .container > aside{
    width:220px; min-height:calc(100vh - 61px);
    background:var(--accent); padding:20px 14px; flex:0 0 220px;
  }
  body .container > aside .posnav{ background:transparent; padding:0; }
  body .container > aside .posnav a{ color:#fff; }
  body .container > aside .posnav a.posnav-active,
  body .container > aside .posnav a:hover{ background:rgba(255,255,255,0.14); }

  body .container > main{
    flex:1; min-width:0; background:var(--muted);
    padding:24px; overflow:visible;
  }
  body .container > main .tablecontainer{
    display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr));
    gap:16px; padding-top:8px;
  }
  body .container > main .tablecontainer .box{
    background:var(--card); border-radius:var(--radius);
    box-shadow:0 2px 8px var(--shadow);
    min-height:150px; padding:14px; gap:10px;
  }
  body .container > main .tablecontainer .box .image{
    width:100%; height:90px; background:#f1f5f2; border-radius:8px;
  }
  body .container > main .tablecontainer .box .add-to-cart,
  body .container > main .tablecontainer .box form [type=submit]{
    background:var(--accent); color:#fff; border:none; border-radius:8px;
    padding:8px 12px; cursor:pointer; width:100%; font-size:14px;
  }
  body .container > main .tablecontainer .box .add-to-cart:hover{ background:#2c866d; }

  body .container .receit{
    width:300px; flex:0 0 300px; background:var(--card);
    color:#222; border-radius:var(--radius);
    box-shadow:0 2px 8px var(--shadow); padding:16px; margin:0; min-height:auto;
  }
  body .container .receit .TILL h4{ color:var(--accent); }
  body .container .receit .tablecontainer1,
  body .container .receit #cart-table{ background:transparent; }
  body .container #cart-table thead th{ color:var(--accent); border-bottom:2px solid var(--accent); font-size:12px; text-align:left; }
  body .container #cart-table td{ border-bottom:1px solid #eef1ef; }
  body .container .cart-actions button{ background:var(--accent); color:#fff; border:none; border-radius:6px; padding:4px 8px; }
  body .container .cart-actions button.remove{ background:var(--danger); }
  body .container .cart-actions .remove{ background:var(--danger); }
  #cart-total{ color:var(--accent); font-weight:700; }
  body .container footer{ width:100%; background:#fff; padding:10px; text-align:center; font-size:12px; color:#64748b; }
  body .container .modal{ color:#222; }
</style>
<style>
  /* ---------- SALES page (.Main/.box1/.box2 + calendar) ---------- */
  body .Main{ display:flex; min-height:100vh; background:var(--muted); }
  body .Main > .box1{
    width:220px; min-height:100vh; background:var(--accent);
    padding:20px 14px; box-sizing:border-box; flex:0 0 220px;
  }
  body .Main > .box1 .nav{ background:transparent; }
  body .Main > .box1 .nav a{ color:#fff; text-align:left; padding:12px 14px; border-radius:8px; }
  body .Main > .box1 .nav a:hover{ background:rgba(255,255,255,0.14); color:#fff; transform:none; }
  body .Main > .box2{
    flex:1; min-width:0; padding:24px; background:var(--muted);
    display:flex; flex-direction:column; gap:20px; max-width:100%;
  }
  body .Main .topnavbar nav a{
    background:#fff; color:var(--accent); border:1px solid var(--accent);
    border-radius:20px; padding:6px 14px; margin-right:8px;
  }
  body .Main .topnavbar nav a:hover{ background:var(--accent); color:#fff; }
  #search{ background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:6px; }
  #search input{ border:none; outline:none; padding:6px 10px; flex:1; }
  #search button{ background:var(--accent); color:#fff; border:none; border-radius:16px; padding:8px 14px; }
  body .Main .saletables{
    background:var(--card); border-radius:var(--radius);
    box-shadow:0 2px 8px var(--shadow); padding:14px; margin-left:0; max-width:100%;
  }
  body .Main .saletables table th{ background:var(--accent); color:#fff; }
  body .Main .saletables table td{ border-bottom:1px solid #eef1ef; }
  body .Main .calendar-wrapper{
    background:var(--card); border-radius:var(--radius);
    box-shadow:0 2px 8px var(--shadow); padding:18px; width:auto; max-width:340px;
  }
  body .Main .calendar-wrapper label, body .Main .calender label{ font-size:13px; color:#444; }
  body .Main .calendar-wrapper select, body .Main .calendar-wrapper input{
    padding:6px 10px; border:1px solid #e2e8f0; border-radius:8px; margin:4px 0 10px;
  }
  body .Main .calendar-wrapper .months{ padding-top:0; }
  body .Main .calendar-wrapper button.submit{ background:var(--accent); color:#fff; border:none; border-radius:8px; padding:8px 16px; }
</style>
<style>
  /* ---------- PRODUCTS page (.main/.box1/.box2 + table) ---------- */
  body .main{ background:var(--muted); }
  body .main .box1{ background:var(--accent); width:220px; min-width:220px; padding:20px 14px; }
  body .main .box1 .nav a{ color:#fff; text-align:left; }
  body .main .box1 .nav a:hover{ background:rgba(255,255,255,0.14); color:#fff; }
  body .main .box2{ background:var(--muted); padding:24px; }
  body .main .box2 .searchbar input{ border-radius:20px; border:1px solid #e2e8f0; padding:8px 12px; }
  body .main .box2 .searchbar button{ background:var(--accent); color:#fff; border:none; border-radius:16px; padding:8px 14px; }
  body .main .box2 .topnav nav a{ background:#fff; color:var(--accent); border:1px solid var(--accent); border-radius:20px; padding:6px 14px; margin-right:6px; }
  body .main .box2 .topnav nav a:hover{ background:var(--accent); color:#fff; }
  body .main .box2 .tablecontainer{ background:var(--card); border-radius:var(--radius); box-shadow:0 2px 8px var(--shadow); padding:12px; }
  body .main .box2 .tablecontainer table{ width:100%; border-collapse:collapse; }
  body .main .box2 .tablecontainer th{ background:var(--accent); color:#fff; padding:10px; text-align:left; border:none; }
  body .main .box2 .tablecontainer td{ background:#fff; border-bottom:1px solid #eef1ef; padding:10px; text-align:left; }
  body .main .box2 .tablecontainer tr:hover td{ background:#f7faf8; }
</style>
<style>
  /* ---------- Shared: tables, buttons, cards ---------- */
  table.data-table{ border-collapse:collapse; width:100%; }
  table.data-table thead th{ background:var(--accent); color:#fff; padding:10px; text-align:left; }
  table.data-table tbody td{ padding:10px; border-bottom:1px solid #eee; vertical-align:middle; }
  table.data-table tbody tr:hover td{ background:#fafafa; }
  .btn, .btn-action.btn-view{ background:var(--accent); color:#fff; border:none; border-radius:8px; padding:8px 14px; cursor:pointer; text-decoration:none; display:inline-block; }
  .btn:hover, .btn-action.btn-view:hover{ background:#2c866d; }
  .card{ background:var(--card); border-radius:var(--radius); box-shadow:0 2px 8px var(--shadow); padding:16px; }
  .muted{ color:#64748b; }
</style>