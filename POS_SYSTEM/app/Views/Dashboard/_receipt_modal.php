<?php
/*
RECEIPT POPUP (MODAL) PARTIAL
Include once per page:  <?= view('Dashboard/_receipt_modal') ?>
Then any View button can call openReceiptModal(saleId) to load the receipt
into this popup via Receipt::view_receipt?modal=1 - no page navigation.
*/
?>
<style>
  .rcpt-overlay{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
                 z-index:1000; align-items:flex-start; justify-content:center;
                 padding:40px 16px; overflow-y:auto; }
  .rcpt-overlay.rcpt-open{ display:flex; }
  .rcpt-modal{ background:#fff; border-radius:12px; max-width:440px; width:100%;
               box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden; }
  .rcpt-head{ display:flex; justify-content:space-between; align-items:center;
              padding:14px 18px; border-bottom:1px solid #e2e8f0; }
  .rcpt-head h3{ color:#0A5741; font-size:1.1rem; margin:0; }
  .rcpt-close{ background:none; border:none; font-size:24px; line-height:1;
               cursor:pointer; color:#64748b; }
  .rcpt-body{ padding:18px; max-height:70vh; overflow-y:auto; }
  .rcpt-foot{ display:flex; gap:10px; padding:0 18px 18px; }
  .rcpt-foot .rcpt-btn{ flex:1; padding:10px; border:none; border-radius:8px;
                        cursor:pointer; font-size:14px; }
  .rcpt-btn-outline{ background:#fff; border:1px solid #0A5741 !important; color:#0A5741; }
  .rcpt-btn-primary{ background:#0A5741; color:#fff; }
  .rcpt-loading{ text-align:center; padding:40px 0; color:#64748b; }
</style>

<div class="rcpt-overlay" id="rcptOverlay">
  <div class="rcpt-modal" role="dialog" aria-modal="true">
    <div class="rcpt-head">
      <h3>Receipt</h3>
      <button type="button" class="rcpt-close" onclick="closeReceiptModal()" aria-label="Close">&times;</button>
    </div>
    <div class="rcpt-body" id="rcptBody">
      <div class="rcpt-loading">Loading receipt&hellip;</div>
    </div>
    <div class="rcpt-foot">
      <button type="button" class="rcpt-btn rcpt-btn-primary" onclick="closeReceiptModal()">Close</button>
      <button type="button" class="rcpt-btn rcpt-btn-outline" onclick="printReceiptModal()">&#128424;&#65039; Print</button>
    </div>
  </div>
</div>

<script>
  function openReceiptModal(saleId){
    var overlay = document.getElementById('rcptOverlay');
    var body    = document.getElementById('rcptBody');
    overlay.classList.add('rcpt-open');
    body.innerHTML = '<div class="rcpt-loading">Loading receipt&hellip;</div>';

    fetch('<?= base_url('receipts/view') ?>/' + encodeURIComponent(saleId) + '?modal=1', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function(r){ if(!r.ok){ throw new Error('HTTP ' + r.status); } return r.text(); })
      .then(function(html){ body.innerHTML = html; })
      .catch(function(err){
        body.innerHTML = '<div class="rcpt-loading">Could not load receipt (' + err.message + ')</div>';
      });
  }

  function closeReceiptModal(){
    document.getElementById('rcptOverlay').classList.remove('rcpt-open');
  }

  function printReceiptModal(){
    var content = document.getElementById('rcptContent');
    if(!content){ return; }
    var win = window.open('', '', 'width=400,height=600');
    win.document.write('<html><head><title>Receipt</title><style>' +
      "body{font-family:'Courier New',monospace;padding:20px;font-size:14px;}" +
      '.r-header{text-align:center;margin-bottom:16px;}' +
      '.r-header h2{font-size:20px;color:#0A5741;}' +
      '.r-header p{font-size:12px;color:#555;}' +
      '.r-divider{border:none;border-top:1px dashed #999;margin:12px 0;}' +
      '.r-meta{font-size:12px;display:flex;justify-content:space-between;margin-bottom:4px;}' +
      '.r-items .r-item{display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;}' +
      '.r-total{display:flex;justify-content:space-between;font-size:16px;font-weight:700;margin-top:8px;}' +
      '.r-footer{text-align:center;font-size:12px;margin-top:20px;color:#555;}' +
      '</style></head><body>' + content.innerHTML + '</body></html>');
    win.document.close();
    win.print();
  }

  /* Click outside the modal closes it */
  document.getElementById('rcptOverlay').addEventListener('click', function(e){
    if(e.target === this){ closeReceiptModal(); }
  });

  /* ESC key closes it */
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){ closeReceiptModal(); }
  });
</script>
