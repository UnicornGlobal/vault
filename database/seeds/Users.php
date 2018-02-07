<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Webpatser\Uuid\Uuid;
use App\User;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::create([
            'id' => 1,
            '_id' => '55A80FE-CA2E-4F69-9E36-81C9237D99B2',
            'app_id' => env('APP_USER'),
            'app_key' => env('APP_PASS'),
            'ip_address' => env('APP_USER_IP'),
        ]);

        /**
         * It's important to only seed dev with this
         *
         * Also used for unit tests
         */
        if (app()->environment('local')) {
            User::create([
                'id' => 2,
                '_id' => '4BFE1010-C11D-4739-8C24-99E1468F08F6',
                'app_id' => '653FDC8C-0FB7-4C72-98F2-2A3A565C7467',
                'app_key' => '396D0273-01BB-4EFF-8C58-750280736D7D',
                'ip_address' => '127.0.0.1'
            ]);
        }
    }
}
