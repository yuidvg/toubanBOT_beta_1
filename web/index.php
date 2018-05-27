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
class toubanTable{
    public $itemNum;
    public $memberNum;
    public $rotateNum;
    public $perWhat;
    public $firstJD;
    public $firstW;
    public $cRW;
    function __construct($itemNum,$memberNum,$rotateNum,$perWhat,$firstJD)
    {
        $this->itemNum = $itemNum;
        $this->memberNum = $memberNum;
        $this->rotateNum = $rotateNum;
        $this->perWhat = $perWhat;
        $this->firstJD = $firstJD;
        $this->firstW = floor(($firstJD+3)/7);
    }
    function rotate($cW)
    {
        $this->cRW = ($cW - $this->firstW) / $this->itemNum;
    }
}

$itemNums = [1, 3, 5];
$memberNums = [3, 5, 5];
$rotateNums = [1, 2, 3];
$perWhat = [WEEK, DAY, WEEK];



for($i=0;$i!=count($itemNums);$i++){
    $toubanTable[$i] = new toubanTable($itemNums[$i],$memberNums[$i],$rotateNums[$i],$perWhat[$i],unixtojd());
}


for($i=0;$i!=count($itemNums);$i++) {
    $toubanTable[$i]->firstW;
}

function getMID($iID){

}
$from = new SendGrid\Email(null, 'nisshi.yui79@gmail.com');
$subject = "当番のお知らせ";
$to = new SendGrid\Email(null, "nisshi.yui79@gmail.com");
$content = new SendGrid\Content("text/plain", "Hello, Email!");
$mail = new SendGrid\Mail($from, $subject, $to, $content);

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
echo $response->headers();
echo $response->body();