<?php

namespace App\Http\Controllers;

use App\Information;
use DataTables;
use DB;
use Illuminate\Http\Request;

class StockController extends Controller {
    private $warning;
    public function __construct() {
        $this->middleware('auth');
        $this->warning = Information::select('stock_warning')->first();
    }
    public function Stock() {
        if (request()->ajax()) {
            $get = DB::select("
SELECT stores.name store,products.product_name,ifnull(sum(purchases.deb_qantity-purchases.cred_qantity),0)-ifnull(sales1.deb_qantity,0) qantity from
products
left join purchases on purchases.product_id=products.id and (purchases.action_id=0 or purchases.action_id=2 or purchases.action_id=3 or purchases.action_id=4 or purchases.action_id=5)
left join (
select product_id,store_id,ifnull(sum(deb_qantity)-sum(cred_qantity),0) deb_qantity from sales where (action_id=0 or action_id=2 or action_id=3) group by product_id,store_id
) as sales1 on (sales1.product_id=products.id and purchases.store_id=sales1.store_id)
left join stores on (sales1.store_id=stores.id or purchases.store_id=stores.id) group by products.id,sales1.store_id,purchases.store_id,purchases.product_id,sales1.product_id order by products.product_name
            	");
//             $get = DB::select("
// SELECT products.product_name,ifnull(sum(purchases.deb_qantity-purchases.cred_qantity),0)-ifnull(sales1.deb_qantity,0) qantity from
// products
// left join purchases on purchases.product_id=products.id and (purchases.action_id=0 or purchases.action_id=2 or purchases.action_id=3)
// left join (
// select product_id,store_id,ifnull(sum(deb_qantity)-sum(cred_qantity),0) deb_qantity from sales where action_id=0 or action_id=2 or action_id=3 group by product_id
// ) as sales1 on sales1.product_id=products.id
// group by products.id,purchases.product_id,sales1.product_id order by products.id
//              ");

            return DataTables::of($get)->addIndexColumn()
                ->setRowAttr([
                    'style' => function ($get) {
                        if (intval($get->qantity) == $this->warning->stock_warning) {
                            return 'background:yellow;color:black;';
                        } elseif (intval($get->qantity) < $this->warning->stock_warning) {
                            return 'background:red;color:white;';
                        }
                    },
                ])->make(true);
        }
        return view('pages.stock.stock');
    }
}
