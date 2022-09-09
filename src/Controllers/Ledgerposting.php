<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Ledgerposting {
    public static function save_ledger_posting($transactionNo, $ledger_id, $ledger_type, $debit, $credit, $module,$date){
        
        $ledger_posting = array(
            'transaction_number' => $transactionNo,
            'ledger_id' =>  $ledger_id,
            'ledger_type' => $ledger_type,
            'debit' => $debit,
            'credit' => $credit,
            'module' => $module,
            'date' => $date,
        );

        Database::table('ledger_postings')->insert($ledger_posting);
    }

    public static function delete_ledger_posting($transactionNo,$module){
        
        Database::table('ledger_postings')->where('transaction_number', $transactionNo)->where('module', $module)->delete();

    }
}