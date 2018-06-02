<?php

require('../vendor/autoload.php');


const DAY = 1;
const WEEK = 7;

$mJD= floor(cal_to_jd(CAL_GREGORIAN,date('n'),date(),date('Y')) - 2400000.5);
$cW = floor(($mJD+3)/7);
$toubanNotfication = '今日の掃除は'."\n";


class toubanTable{
    public $itemNum;
    public $memberNum;
    public $rotateNum;
    public $perWhat;
    public $firstJD;
    public $firstW;
    public $rotation;

    function __construct($itemNum, $memberNum, $rotateNum, $perWhat, $firstJD){
        $this->itemNum = $itemNum;
        $this->memberNum = $memberNum;
        $this->rotateNum = $rotateNum;
        $this->perWhat = $perWhat;
        $this->firstJD = $firstJD;
        $this->firstW = floor(($firstJD + 3) / 7);
    }

    function getMID($iID){
        $this->rotation = $GLOBALS['cW'] - $this->firstW;
        return ($iID - gmp_mod($this->rotation * $this->rotateNum, max($this->memberNum,$this->itemNum)));
    }

    function output(){
        for($i = 1; $i <= $this -> itemNum; $i++){
            if($this->getMID($i) != 0 && $this->getMID($i) <= $this->memberNum) $GLOBALS['toubanNotfication'] .= "$i".'番目の役割は'."getMID($i)".'さんが当番です'."\n";
        }
    }
}

function time_diff($time_from, $time_to)
{
    // 日時差を秒数で取得
    $dif = $time_to - $time_from;
    // 時間単位の差
    $dif_time = date("H:i:s", $dif);
    // 日付単位の差
    $dif_days = (strtotime(date("Y-m-d", $dif)) - strtotime("1970-01-01")) / 86400;
    return "{$dif_days}days {$dif_time}";
}



$itemNums = [1, 3, 5];
$memberNums = [3, 2, 5];
$rotateNums = [1, 2, 3];
$perWhat = [WEEK, WEEK, WEEK];



for($i = 0; $i != count($itemNums); $i++){
    $toubanTable[$i] = new toubanTable($itemNums[$i],$memberNums[$i],$rotateNums[$i],$perWhat[$i],cal_to_jd(CAL_GREGORIAN,date('n'),date(),date('Y')));
    $toubanTable[$i]->output;
}



$post_data = array(
    "value1" => "$toubanNotfication",
    "value2" => "waaaa",
    "value3" => "rr"
);
//IFTTT
$ch = curl_init('https://maker.ifttt.com/trigger/toubanbot1/with/key/rBrhvXD3WeFcdEEwJl6ht');

curl_setopt($ch,CURLOPT_POST, true);

//データの配列を設定する
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

curl_exec($ch);
curl_close($ch);

