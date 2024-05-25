<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantReportExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();
        $this->transaction = new Transaction;
        $transactions = $this->transaction->getMerchantTransactionReports($input);
        
        $CompanyName = "";
        if(isset($input['user_id']) && !empty($input['user_id'])){

            $companyDetails = \DB::table('applications')
            ->join('users','users.id','applications.user_id')
            ->where('users.id', $input['user_id'])->first();
            if(!empty($companyDetails)){
                $CompanyName = $companyDetails->business_name;
            }
        } else {
            $transactions = array();
        }
        $data = [];
        foreach ($transactions as $key => $transaction) {
            
            $datatmp['merchant'] = $CompanyName;
            $datatmp['created_date'] = $transaction['created_date'];
            $datatmp['total_processing_amount'] = $transaction['total_processing_amount'];
            $datatmp['approved_amount'] = $transaction['approved_amount'];
            $datatmp['declined_amount'] = $transaction['declined_amount'];
            $datatmp['chargeback_amount'] = $transaction['chargeback_amount'];
            $datatmp['refund_amount'] = $transaction['refund_amount'];
            $datatmp['flagged_amount'] = $transaction['flagged_amount'];
            $datatmp['pre_arbitration_amount'] = $transaction['pre_arbitration_amount'];
            $datatmp['approved_count'] = $transaction['approved_count'];
            $datatmp['declined_count'] = $transaction['declined_count'];
            $datatmp['chargeback_count'] = $transaction['chargeback_count'];
            $datatmp['refund_count'] = $transaction['refund_count'];
            $datatmp['flagged_count'] = $transaction['flagged_count'];
            $datatmp['pre_arbitration_count'] = $transaction['pre_arbitration_count'];
            $datatmp['total_no_of_transactions_count'] = $transaction['total_no_of_transactions_count'];
            $datatmp['mdr'] = $transaction['mdr'];
            $datatmp['reserve'] = $transaction['reserve'];
            $datatmp['transaction_fee'] = $transaction['transaction_fee'];
            $datatmp['refund_fee'] = $transaction['refund_fee'];
            $datatmp['high_risk_transaction_fee'] = $transaction['high_risk_transaction_fee'];
            $datatmp['chargeback_fee'] = $transaction['chargeback_fee'];
            $datatmp['total_payable'] = $transaction['total_payable'];
            $datatmp['gross_payable'] = $transaction['gross_payable'];
            $datatmp['net_payable'] = $transaction['net_payable'];
            $datatmp['status'] = $transaction['status'];
            $data[] = $datatmp;
        }
        return collect($data);
    }

	public function map($data): array
    {
        $data['total_processing_amount'] = (string) round($data['total_processing_amount'], 2);
        $data['approved_amount'] = (string) round($data['approved_amount'], 2);
        $data['declined_amount'] = (string) round($data['declined_amount'], 2);
        $data['chargeback_amount'] = (string) round($data['chargeback_amount'], 2);
        $data['refund_amount'] = (string) round($data['refund_amount'], 2);
        $data['flagged_amount'] = (string) round($data['flagged_amount'], 2);
        $data['pre_arbitration_amount'] = (string) round($data['pre_arbitration_amount'], 2);
        $data['approved_count'] = (string) round($data['approved_count'], 2);
        $data['declined_count'] = (string) round($data['declined_count'], 2);
        $data['chargeback_count'] = (string) round($data['chargeback_count'], 2);
        $data['refund_count'] = (string) round($data['refund_count'], 2);
        $data['flagged_count'] = (string) round($data['flagged_count'], 2);
        $data['pre_arbitration_count'] = (string) round($data['pre_arbitration_count'], 2);
        $data['total_no_of_transactions_count'] = (string) round($data['total_no_of_transactions_count'], 2);
        $data['mdr'] = (string) round($data['mdr'], 2);
        $data['reserve'] = (string) round($data['reserve'], 2);
        $data['transaction_fee'] = (string) round($data['transaction_fee'], 2);
        $data['refund_fee'] = (string) round($data['refund_fee'], 2);
        $data['high_risk_transaction_fee'] = (string) round($data['high_risk_transaction_fee'], 2);
        $data['chargeback_fee'] = (string) round($data['chargeback_fee'], 2);
        $data['total_payable'] = (string) round($data['total_payable'], 2);
        $data['gross_payable'] = (string) round($data['gross_payable'], 2);
        $data['net_payable'] = (string) round($data['net_payable'], 2);
        $data['status'] = (string) round($data['status'], 2);
        return $data;
    }

    public function headings(): array
    {
        return [
            'Merchant',
            'Date',
            'Total Processing Amount',
            'Approved Amount',
            'Declined Amount',
            'ChargeBack Amount',
            'Refund Amount',
            'Flagged Amount',
            'Pre Arbitration Amount',
            'Approved Count',
            'Declined Count',
            'ChargeBack Count',
            'Refund Count',
            'Flagged Count',
            'Pre Arbitration Count',
            'Pre Arbitration Count',
            'MDR',
            'Reserve',
            'Transaction Fee',
            'Refund Fee',
            'High Risk Transaction Fee',
            'ChargeBack Fee',
            'Total Payable',
            'Gross Payable',
            'Net Payable',
            'status'
        ];
    }
}
