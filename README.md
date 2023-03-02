# elnashra.com

## Setup
```
$ cd elnashra.com
#create .env.local file
$ chmod 777 -R var/
$ chmod 777 public/bash/cron*
$ composer update
$ php bin/console cache:clear --env=prod --no-debug
```

## Compile Assets on Local machine
```
$ yarn install # make sure Yarn is installed
$ yarn encore prod # generate css/js files
```
