<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Information;
use App\MultiSetting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class MakeUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class,1)->create();
        factory(Information::class,1)->create();
        factory(Role::class,1)->create();
        
        $role=Role::first();
        $user=User::first();
        $setting[]=['name'=>'invoice_vat'];
        $setting[]=['name'=>'invoice_discount'];
        $setting[]=['name'=>'invoice_labour'];
        $setting[]=['name'=>'invoice_transport'];
        $setting[]=['name'=>'purchase_labour'];
        $setting[]=['name'=>'purchase_transport'];
        $setting[]=['name'=>'language'];
        for($i=0;$i<count($setting);$i++){
            MultiSetting::create(['name'=>$setting[$i]['name'],'value'=>1]);
        }
        $user->assignRole(strval($role->name));
    }
}
