<?php


namespace App\Controllers;

use CodeIgniter\Controller;

class Reports extends Controller
{
    /**
     * Export Loans to CSV
     */
    public function loansExport()
    {
        $db = \Config\Database::connect();
        
        // Get filter status
        $loanStatus = $this->request->getGet('loan_status') ?? 'all';
        
        // Build query
        $query = $db->table('loans')
            ->select('loans.*, customers.fullname as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = loans.customer_id', 'left');
        
        if ($loanStatus !== 'all') {
            $query->where('loans.status', $loanStatus);
        }
        
        $loans = $query->orderBy('loans.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
        
        // Generate CSV
        $this->generateCSV('loan_report', $loans, [
            'id' => 'Loan ID',
            'customer_name' => 'Borrower Name',
            'customer_email' => 'Email',
            'amount' => 'Amount',
            'term' => 'Term (Months)',
            'interest_rate' => 'Interest Rate',
            'status' => 'Status',
            'created_at' => 'Applied Date',
        ]);
    }

    /**
     * Export KYC to CSV
     */
    public function kycExport()
    {
        $db = \Config\Database::connect();
        
        // Get filter status
        $kycStatus = $this->request->getGet('kyc_status') ?? 'all';
        
        // Build query
        $query = $db->table('kyc')
            ->select('kyc.*, customers.fullname as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = kyc.customer_id', 'left');
        
        if ($kycStatus !== 'all') {
            $query->where('kyc.status', $kycStatus);
        }
        
        $kycs = $query->orderBy('kyc.created_at', 'DESC')
                      ->get()
                      ->getResultArray();
        
        // Generate CSV
        $this->generateCSV('kyc_report', $kycs, [
            'id' => 'KYC ID',
            'customer_name' => 'Customer Name',
            'customer_email' => 'Email',
            'id_type' => 'ID Type',
            'id_number' => 'ID Number',
            'employment_status' => 'Employment',
            'monthly_income' => 'Monthly Income',
            'status' => 'Status',
            'created_at' => 'Submitted Date',
        ]);
    }

    /**
     * Export Customers to CSV
     */
    public function customersExport()
    {
        $db = \Config\Database::connect();
        
        // Get all customers with loan count
        $customers = $db->table('customers')
            ->select('customers.id, customers.fullname, customers.email, customers.contact_number, customers.gender, customers.civil_status, customers.is_verified, customers.created_at, COUNT(loans.id) as total_loans')
            ->join('loans', 'loans.customer_id = customers.id', 'left')
            ->groupBy('customers.id')
            ->orderBy('customers.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Generate CSV
        $this->generateCSV('customers_report', $customers, [
            'id' => 'Customer ID',
            'fullname' => 'Full Name',
            'email' => 'Email',
            'contact_number' => 'Phone',
            'gender' => 'Gender',
            'civil_status' => 'Civil Status',
            'is_verified' => 'Verified',
            'total_loans' => 'Total Loans',
            'created_at' => 'Member Since',
        ]);
    }

    /**
     * Helper function to generate CSV
     */
    private function generateCSV($filename, $data, $headers)
    {
        // Set headers for download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d_H-i-s') . '.csv"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write CSV header row
        fputcsv($output, array_values($headers));
        
        // Write data rows
        foreach ($data as $row) {
            $csvRow = [];
            foreach (array_keys($headers) as $key) {
                $value = $row[$key] ?? '';
                
                // Format specific fields
                if ($key === 'is_verified') {
                    $value = $value ? 'Yes' : 'No';
                } elseif (strpos($key, '_at') !== false || strpos($key, 'date') !== false) {
                    if (!empty($value)) {
                        $value = date('M d, Y H:i', strtotime($value));
                    }
                } elseif (strpos($key, 'amount') !== false || strpos($key, 'income') !== false) {
                    if (!empty($value)) {
                        $value = '₱' . number_format($value, 2);
                    }
                }
                
                $csvRow[] = $value;
            }
            fputcsv($output, $csvRow);
        }
        
        fclose($output);
        exit;
    }
}