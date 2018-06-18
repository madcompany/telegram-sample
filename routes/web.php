<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->post('webhook',    'TelegramController@webhook'); //Telegram Webhook URI

$app->get('test',                  'TelegramController@test'); //Telegram Webhook URI
$app->get('active',                  'TelegramController@active'); //Telegram Webhook URI

$app->get('test2',                  'TelegramController@test2'); //Telegram Webhook URI

$app->get('oauth', 'TelegramController@oauth');//인증
$app->get('callback', 'TelegramController@callback');//인증

//Telegram Router
/*
$app->group(['prefix' => 'telegram'], function () use ($app){


    $app->post('webhook',                  'TelegramController@webhook'); //Telegram Webhook URI

    $app->get( 'test',                     'TelegramController@test'); //test
});
*/