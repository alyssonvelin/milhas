<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApimilhasController extends Controller
{
    public function index()
    {
        $contents = json_decode(static::get_page('http://prova.123milhas.net/api/flights'), true);
        $inbounds = array();
        $outbounds = array();
        $types = array();
        $possibilities = array();
        $finalResult = array();
        if($contents)
        {
            foreach($contents as $key => $value)
            {
                $types[$value['fare']] = $value['fare'];
                if($value['outbound'])
                    $outbounds[$value['fare']][] = $value;
                if($value['inbound'])
                    $inbounds[$value['fare']][] = $value;
            }
            //echo '<pre>';var_dump($outbounds,$inbounds);exit;
            if($types)
            {
                $groups = array();
                $idGroup = 1;
                foreach($types as $valueType)
                {
                    $tot = 0;
                    foreach($outbounds[$valueType] as $keyOut => $valueOut)
                    {
                        foreach($inbounds[$valueType] as $keyIn => $valueIn)
                        {
                            $possibilities[$valueType][] = array('idOut'=>$valueOut['id'],'idIn'=>$valueIn['id'],'tot'=>$valueOut['price']+$valueIn['price']);
                        }
                    }
                    usort($possibilities[$valueType],function($a, $b)
                    {
                        if($a['tot'] == $b['tot'])
                        {
                            return 0;
                        }
                        return ($a['tot'] < $b['tot']) ? -1 : 1;
                    });
                    if($possibilities[$valueType])
                    {
                        foreach($possibilities[$valueType] as $key => $value)
                        {
                            if($tot == 0)
                            {
                                if($idGroup > 1)
                                    $idGroup++;
                                $tot = $value['tot'];
                                $groups[$idGroup]['uniqueId'] = $idGroup;
                                $groups[$idGroup]['totalPrice'] = $value['tot'];
                                $groups[$idGroup]['outbound'] = array();
                                $groups[$idGroup]['inbound'] = array();
                            }
                            else if($tot > 0 && $tot == $value['tot'])
                            {
                                if(!in_array($value['idOut'],$groups[$idGroup]['outbound']))
                                    $groups[$idGroup]['outbound'][] = $value['idOut'];
                                if(!in_array($value['idIn'],$groups[$idGroup]['inbound']))
                                    $groups[$idGroup]['inbound'][] = $value['idIn'];
                            }
                            else if($tot > 0 && $tot != $value['tot'])
                            {
                                $idGroup++;
                                $tot = $value['tot'];
                                $groups[$idGroup]['uniqueId'] = $idGroup;
                                $groups[$idGroup]['totalPrice'] = $value['tot']; 
                                $groups[$idGroup]['outbound'] = array();
                                $groups[$idGroup]['inbound'] = array();
                                if(!in_array($value['idOut'],$groups[$idGroup]['outbound']))
                                    $groups[$idGroup]['outbound'][] = $value['idOut'];
                                if(!in_array($value['idIn'],$groups[$idGroup]['inbound']))
                                    $groups[$idGroup]['inbound'][] = $value['idIn'];
                                
                            }
                        }
                    }
                }
            }
            $finalResult['flights'] = $contents;
            $finalResult['groups'] = $groups;
            $finalResult['totalGroups'] = count($groups);
            $finalResult['totalFlights'] = 0;
            $finalResult['cheapestPrice'] = $groups[1]['totalPrice'];
            $finalResult['cheapestGroup'] = $groups[1]['uniqueId'];
        }

        return $finalResult;
        
          
          
        //dd(json_encode($finalResult));
        echo '<pre>';var_dump(json_encode($finalResult));exit;
        
        
        
    }

    public static function get_page($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
    }
}
