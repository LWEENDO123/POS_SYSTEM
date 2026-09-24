<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueGatewayReferenceToPaymentTransactions extends Migration
{
    public function up()
    {
        // ============================================================
        // IDEMPOTENCY: Unique index on gateway_reference
        // ============================================================
        // 
        // PROBLEM: Without this, two simultaneous requests with the same
        //          gateway_reference can both pass the PHP check and both insert
        //          (race condition between SELECT and INSERT)
        // 
        // SOLUTION: Add a UNIQUE INDEX at the DATABASE level
        //           The database will REJECT the second insert with an error
        //           This is the ULTIMATE protection - cannot be bypassed
        // 
        // IMPORTANT: Run this migration AFTER cleaning up any existing
        //            duplicate gateway_reference values in the table!
        //            Otherwise the migration will fail.
        // 
        // To clean duplicates first, run a query like:
        // DELETE t1 FROM paymenttransactions t1
        // INNER JOIN paymenttransactions t2 
        // WHERE t1.transaction_id > t2.transaction_id 
        // AND t1.gateway_reference = t2.gateway_reference;
        // ============================================================
        
        // Check if the unique key already exists (idempotent migration)
        $fields = $this->db->getFieldData('paymenttransactions');
        $hasUniqueKey = false;
        
        // Get existing indexes
        $indexes = $this->db->query("SHOW INDEX FROM paymenttransactions WHERE Key_name = 'unique_gateway_ref'")->getResultArray();
        if (!empty($indexes)) {
            $hasUniqueKey = true;
        }
        
        if (!$hasUniqueKey) {
            $this->forge->addKey('gateway_reference', true, false, 'unique_gateway_ref');
            $this->forge->processIndexes('paymenttransactions');
        }
    }

    public function down()
    {
        // Remove the unique index if rolling back
        $this->forge->dropKey('unique_gateway_ref', 'paymenttransactions');
    }
}