# Composer Late Upload

This project contains a prototype for mirroring composer dist urls in a easy way.

## Idea

Instead of creating the whole composer/packagist context beforehand, it could be done afterwards.

This application contains a script and a webservice to provide this.

## Installation

This Repo can be deployed as is to heroku.
It will need ENV vars as configuration, that could also be added in the `config/config.php`

```
heroku config:set DROPBOX_APPNAME=Appname
heroku config:set DROPBOX_TOKEN=YourDropboxToken
heroku config:set GITHUB_TOKEN=YourGithubToken
```

## Steps

1. Run `composer update` or `composer install` as usual.
2. Run `php bin/composer-late-upload composer.lock` to rewrite the `dist` urls in your `composer.lock`.
3. Following installations will use the dist url given by this webservice.

## Improvements

Its currently using flysystem and dropbox, but could be easily replaced to use something else like Amazon S3 or rackspace.

## License

Its MIT.