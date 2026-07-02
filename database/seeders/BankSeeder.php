<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name' => 'State Bank of India', 'ifsc_prefix' => 'SBIN'],
            ['name' => 'Bank of Baroda', 'ifsc_prefix' => 'BARB'],
            ['name' => 'Punjab National Bank', 'ifsc_prefix' => 'PUNB'],
            ['name' => 'Canara Bank', 'ifsc_prefix' => 'CNRB'],
            ['name' => 'Bank of India', 'ifsc_prefix' => 'BKID'],
            ['name' => 'Union Bank of India', 'ifsc_prefix' => 'UBIN'],
            ['name' => 'Indian Bank', 'ifsc_prefix' => 'IDIB'],
            ['name' => 'Central Bank of India', 'ifsc_prefix' => 'CBIN'],
            ['name' => 'Indian Overseas Bank', 'ifsc_prefix' => 'IOBA'],
            ['name' => 'UCO Bank', 'ifsc_prefix' => 'UCBA'],
            ['name' => 'Bank of Maharashtra', 'ifsc_prefix' => 'MAHB'],
            ['name' => 'Punjab & Sind Bank', 'ifsc_prefix' => 'PSIB'],
            ['name' => 'HDFC Bank', 'ifsc_prefix' => 'HDFC'],
            ['name' => 'ICICI Bank', 'ifsc_prefix' => 'ICIC'],
            ['name' => 'Axis Bank', 'ifsc_prefix' => 'UTIB'],
            ['name' => 'Kotak Mahindra Bank', 'ifsc_prefix' => 'KKBK'],
            ['name' => 'Yes Bank', 'ifsc_prefix' => 'YESB'],
            ['name' => 'IndusInd Bank', 'ifsc_prefix' => 'INDB'],
            ['name' => 'IDBI Bank', 'ifsc_prefix' => 'IBKL'],
            ['name' => 'South Indian Bank', 'ifsc_prefix' => 'SIBL'],
            ['name' => 'Federal Bank', 'ifsc_prefix' => 'FDRL'],
            ['name' => 'IDFC FIRST Bank', 'ifsc_prefix' => 'IDFB'],
            ['name' => 'Bandhan Bank', 'ifsc_prefix' => 'BDBL'],
            ['name' => 'RBL Bank', 'ifsc_prefix' => 'RATN'],
            ['name' => 'DCB Bank', 'ifsc_prefix' => 'DCBL'],
            ['name' => 'Jammu & Kashmir Bank', 'ifsc_prefix' => 'JAKA'],
            ['name' => 'Karur Vysya Bank', 'ifsc_prefix' => 'KVBL'],
            ['name' => 'Tamilnad Mercantile Bank', 'ifsc_prefix' => 'TMBL'],
            ['name' => 'City Union Bank', 'ifsc_prefix' => 'CIUB'],
            ['name' => 'Dhanlaxmi Bank', 'ifsc_prefix' => 'DLXB'],
            ['name' => 'Karnataka Bank', 'ifsc_prefix' => 'KARB'],
            ['name' => 'Catholic Syrian Bank', 'ifsc_prefix' => 'CSBK'],
            ['name' => 'Lakshmi Vilas Bank', 'ifsc_prefix' => 'LAVB'],
            ['name' => 'Saraswat Bank', 'ifsc_prefix' => 'SRCB'],
            ['name' => 'Shamrao Vithal Co-operative Bank', 'ifsc_prefix' => 'SVCB'],
            ['name' => 'Abhyudaya Co-operative Bank', 'ifsc_prefix' => 'ABHY'],
            ['name' => 'NKGSB Co-operative Bank', 'ifsc_prefix' => 'NKGS'],
            ['name' => 'TJSB Sahakari Bank', 'ifsc_prefix' => 'TJSB'],
            ['name' => 'Apna Sahakari Bank', 'ifsc_prefix' => 'ASBL'],
            ['name' => 'Greater Bombay Co-operative Bank', 'ifsc_prefix' => 'GBCB'],
            ['name' => 'Bassein Catholic Co-operative Bank', 'ifsc_prefix' => 'BACB'],
            ['name' => 'Janata Sahakari Bank (Pune)', 'ifsc_prefix' => 'JSBP'],
            ['name' => 'Rajkot Nagrik Sahakari Bank', 'ifsc_prefix' => 'RNSB'],
            ['name' => 'Surat Nagrik Sahakari Bank', 'ifsc_prefix' => 'SNSB'],
            ['name' => 'Kalupur Commercial Co-operative Bank', 'ifsc_prefix' => 'KCCB'],
            ['name' => 'Mehsana Urban Co-operative Bank', 'ifsc_prefix' => 'MSNU'],
            ['name' => 'Nutun Nagrik Sahakari Bank', 'ifsc_prefix' => 'NNSB'],
            ['name' => 'Varachha Co-operative Bank', 'ifsc_prefix' => 'VARA'],
            ['name' => 'AU Small Finance Bank', 'ifsc_prefix' => 'AUBL'],
            ['name' => 'Utkarsh Small Finance Bank', 'ifsc_prefix' => 'UTKS'],
            ['name' => 'Equitas Small Finance Bank', 'ifsc_prefix' => 'ESFB'],
            ['name' => 'Fincare Small Finance Bank', 'ifsc_prefix' => 'FSFB'],
            ['name' => 'Suryoday Small Finance Bank', 'ifsc_prefix' => 'SURY'],
            ['name' => 'Ujjivan Small Finance Bank', 'ifsc_prefix' => 'UJVN'],
            ['name' => 'Jana Small Finance Bank', 'ifsc_prefix' => 'JSFB'],
            ['name' => 'Paytm Payments Bank', 'ifsc_prefix' => 'PYTM'],
            ['name' => 'Airtel Payments Bank', 'ifsc_prefix' => 'AIRP'],
            ['name' => 'India Post Payments Bank', 'ifsc_prefix' => 'IPPB'],
            ['name' => 'NSDL Payments Bank', 'ifsc_prefix' => 'NSPB'],
            ['name' => 'Fino Payments Bank', 'ifsc_prefix' => 'FINO'],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
