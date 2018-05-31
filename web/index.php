<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->run();

const DAY = 1;
const WEEK = 7;

$mJD= floor(unixtojd() - 2400000.5);
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
$itemNums = [1, 3, 5];
$memberNums = [3, 2, 5];
$rotateNums = [1, 2, 3];
$perWhat = [WEEK, WEEK, WEEK];



for($i = 0; $i != count($itemNums); $i++){
    $toubanTable[$i] = new toubanTable($itemNums[$i],$memberNums[$i],$rotateNums[$i],$perWhat[$i],unixtojd());
    $toubanTable[$i]->output;
}



$post_data = array(
    "value1" => "aa",
    "value2" => "ww",
    "value3" => "rr"
);
//IFTTT
$url = "https://maker.ifttt.com/trigger/toubanbot1/with/key/rBrhvXD3WeFcdEEwJl6ht";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);

//データの配列を設定する
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

curl_exec($ch);
curl_close($ch);


$from = new SendGrid\Email(null, 'nisshi.yui79@gmail.com');
$subject = "当番のお知らせ";
$to = new SendGrid\Email(null, "nisshi.yui79@gmail.com");
$content = new SendGrid\Content("text/plain", "$toubanNotfication");
$mail = new SendGrid\Mail($from, $subject, $to, $content);

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
echo $response->headers();
echo $response->body();