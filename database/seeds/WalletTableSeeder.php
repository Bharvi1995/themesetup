<?php

use App\Wallet;
use Illuminate\Database\Seeder;

class WalletTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Wallet::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $wallets = [
            ['name' => 'Bitcoin','code' => 'BTC'],
            ['name' => 'Ethereum','code' => 'ETH'],
            ['name' => 'Tether','code' => 'USDT'],
            ['name' => 'USD Coin','code' => 'USDC'],
            ['name' => 'BNB','code' => 'BNB']
        ];

        // Add all wallets to the table
        Wallet::insert($wallets);

        $date_upd = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        Wallet::whereNull('created_at')->update($date_upd);
    }
}
