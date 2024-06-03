<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $idUser = User::insertGetId([
            'uuid' =>  Str::uuid(),
            'nama_lengkap'=>'admin',
            'email'=>'Admin@gmail.com',
            'password'=>Hash::make('Admin@1234567890'),
            'role'=>'admin',
            'foto'=>'admin.jpg',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ]);
        $directory = storage_path('app/admin');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        Storage::disk('admin')->put('/admin.jpg', Crypt::encrypt(file_get_contents(database_path('seeders/image/admin.jpg'))));
        $i = 0;
        //insert device & laporan
        for($i; $i <= 9; $i++){
            Device::insert([
                'uuid' =>  Str::uuid(),
                'nama_device'=>Str::random(10),
                'device_id'=> Str::random(7).'-'.Str::random(7).'-'.Str::random(7),
                'device_token'=>Str::random(64),
                'actived'=>false,
                'email'=>'Admin@gmail.com',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
                'id_user'=>$idUser,
            ]);
            Schema::create('laporan_'.$i+1, function (Blueprint $table) use($i) {
                $table->id('id_laporan');
                $table->integer('data');
                $table->timestamps();
                $table->unsignedBigInteger('id_device');
                $table->foreign('id_device', "fk_laporan_id".$i+1)->references('id_device')->on('device');
            });
            for($n = 0; $n <= 9; $n++){
                DB::table('laporan_'.$i+1)->insert([
                    'data' =>  mt_rand(00, 99),
                    'id_device'=>$i+1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $idUser = User::insertGetId([
            'uuid' =>  Str::uuid(),
            'nama_lengkap'=>'user',
            'email'=>'User@gmail.com',
            'password'=>Hash::make('Admin@1234567890'),
            'role'=>'user',
            'foto'=>'user.jpeg',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ]);
        $directory = storage_path('app/user');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        Storage::disk('user')->put('/user.jpeg', Crypt::encrypt(file_get_contents(database_path('seeders/image/user.jpeg'))));
        //insert device & laporan
        for($i; $i > 9 && $i <= 19; $i++){
            Device::insert([
                'uuid' =>  Str::uuid(),
                'nama_device'=>Str::random(10),
                'device_id'=> Str::random(7).'-'.Str::random(7).'-'.Str::random(7),
                'device_token'=>Str::random(64),
                'actived'=>false,
                'email'=>'User@gmail.com',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
                'id_user'=>$idUser,
            ]);
            Schema::create('laporan_'.$i+1, function (Blueprint $table) use($i) {
                $table->id('id_laporan');
                $table->integer('data');
                $table->timestamps();
                $table->unsignedBigInteger('id_device');
                $table->foreign('id_device', "fk_laporan_id".$i+1)->references('id_device')->on('device');
            });
            for($n = 0; $n <= 9; $n++){
                DB::table('laporan_'.$i+1)->insert([
                    'data' =>  mt_rand(00, 99),
                    'id_device'=>$i+1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $directory = storage_path('app/database');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}