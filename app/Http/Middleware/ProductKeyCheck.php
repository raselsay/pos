<?php

namespace App\Http\Middleware;

use Closure;
use App\Keycheck;
class ProductKeyCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        // dd($request->getPathInfo());
        switch(true){
            case ($request->method()=='GET' and ($request->getPathInfo()=='/login' or $request->getPathInfo()=='/')):
                return $next($request);
                break;
            case $request->getPathInfo()=='/logout' :
                return $next($request);
                break;
            case $request->getPathInfo()=="/lisencekey":
                return $next($request);
                break;
        }
        if ($this->check_internet('www.php.net')){
           $todate=Keycheck::select('todate')->first();
            $defaults = array(
            CURLOPT_URL             => 'https://timezone.abstractapi.com/v1/current_time?api_key=56ffdb8e8d7f47f8baaf36a5e2cae5ed&location=Dhaka,Bangladesh',
            CURLOPT_POST            => false,
            CURLOPT_HEADER          => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYHOST  => false,
            );
            $curl               = curl_init();
            curl_setopt_array($curl, $defaults);
            $curl_response      = curl_exec($curl);
            curl_close($curl);
            $json_object        = json_decode($curl_response);
            // dd($curl_response);
            $internetdate= $json_object->datetime;
            $internetdate=explode(" ",$internetdate);
            $internetdate=strtotime(strval($internetdate[0]));
            $internetdate=intval(strtotime(strval(date('d-m-Y',intval($internetdate)))));
            if(isset($todate->todate)){
                $todate=intval(strtotime(strval(date('d-m-Y',intval($todate->todate)))));
            }
            // dd(intval($internetdate)<=intval($todate));
            switch(true) {
                case $todate==null;
                $keycheck=new Keycheck;
                $keycheck->fromdate=$internetdate;
                $keycheck->todate=strtotime('+1 month',strtotime(date('d-m-Y',$internetdate)));
                $keycheck->key='trial period';
                $keycheck->save();
                   return $next($request);
                    break;
                case intval($internetdate)<=intval($todate) :
                    return $next($request);
                    break;
                case intval($internetdate)>intval($todate) :
                    return redirect('/lisencekey')->with(['dateover'=>'Your Software Date Over Please Contact With This Software Owner For Lisence Key']);
                    break;
            }
        }else{
            return redirect()->back()->with(['internet'=>'Internet Connection Failed!! Please Check Your Internet Connection']);
        }

        return $next($request);
        
    }


    private function check_internet($domain)
    {
        $file = @fsockopen ($domain, 80);//@fsockopen is used to connect to a socket
        // return false;
        return ($file);
    }
}
