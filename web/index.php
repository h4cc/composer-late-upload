<?php

require_once __DIR__.'/../vendor/autoload.php';

use Dropbox\Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Dropbox as Adapter;
use Symfony\Component\HttpFoundation\Response;

$token = getenv('DROPBOX_TOKEN');
$appName = getenv('DROPBOX_APPNAME');
$githubToken = getenv('GITHUB_TOKEN');

if(file_exists(__DIR__.'/../config/config.php')) {
    require_once(__DIR__.'/../config/config.php');
}

$client = new Client($token, $appName);
$filesystem = new Filesystem(new Adapter($client, ''));

$github = new Github\Client();
$github->authenticate($githubToken, 'http_token');

$app = new Silex\Application();
$app['debug'] = true;

$app['github'] = $github;
$app['dropbox.client'] = $client;
$app['storage'] = $filesystem;

$app->get('/', function() use($app) {
    return 'This is a example composer dist storage system.';
});

$app->get('/{user}/{repo}/zipball/{ref}', function($user, $repo, $ref) use($app) {

    $path = $user.'__'.$repo.'__'.$ref;

    if(!$app['storage']->has($path)) {

        $content = $app['github']->api('repo')->contents()->archive($user, $repo, 'zipball', $ref);

        if(!$content) {
            return new Response('Could not fetch content from github', 500);
        }

        $result = $app['storage']->write($path, $content);

        if(!$result) {
            return new Response('Could not write to storage', 500);
        }
    }

    $link = $app['dropbox.client']->createShareableLink('/'.$path);

    return $app->json([
        'link' => $link
    ]);
});

$app->run(); 