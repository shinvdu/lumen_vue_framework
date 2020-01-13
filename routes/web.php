<?php

$app->get('/version', function () use ($app) {
    return $app->version();
});

$app->get('/', [
    'as' => 'index',
    'uses' => 'IndexController@redirOauth'
    // return view('index', ['name' => 'James']);
]);

$app->get('/oauth/redir', [
    'as' => 'wechatoauth',
    'uses' => 'OAuthController@redirOauth'
]);

$app->get('/oauth/callback', [
    'as' => 'wechatoauth',
    'uses' => 'OAuthController@OauthCallback'
]);

 
$app->get('/logout', [
    'as' => 'index.logout',
    'uses' => 'IndexController@logout'
]);

$app->get('/reg_grant', [
    'as' => 'index.reg_grant',
    'uses' => 'IndexController@grantRegisteredUser'
]);

$app->get('/gz_resource/{file_name}', [
    'as' => 'index.get_gz_file',
    'uses' => 'IndexController@getGzFile'
]);

//测试直接登录
$app->get('/test_grant', [
    'as' => 'test_grant',
    'uses' => 'IndexController@getTestGrant'
]);
