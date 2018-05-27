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

echo 'hello';

const DAY = 1;
const WEEK = 7;

class toubanTable{
    public $itemNum;
    public $memberNum;
    public $rotateNum;
    public $perWhat;

    function __construct($itemNum,$memberNum,$rotateNum,$perWhat)
    {
        $this->itemNum=$itemNum;
        $this->memberNum=$memberNum;
        $this->rotateNum=$rotateNum;
        $this->perWhat=$perWhat;
    }
}



$itemNums = [1, 3, 5] ;
$memberNums = [3, 5, 5] ;
$rotateNums = [1, 2, 3];
$perWhat = [WEEK, DAY, WEEK];

for($i=0;$i!=count($itemNums);$i++){
    $toubanTable[$i] = new toubanTable($itemNums[$i],$memberNums[$i],$rotateNums[$i],$perWhat[$i]);
}

$from = new SendGrid\Email(null, "nisshi.yui79@gmail.com");
$subject = "Hello World from the SendGrid PHP Library!";
$to = new SendGrid\Email(null, "nisshi.yui79@gmail.com");
$content = new SendGrid\Content("text/plain", "Hello, Email!");
$mail = new SendGrid\Mail($from, $subject, $to, $content);

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
echo $response->headers();
echo $response->body();