# suyool.com

# if orders get errorInfo 53 it means limit Reached from backend

## Setup
```
$ cd suyool.com
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

## Command to eslint assets run
```
$ eslint ./
$ npx eslint assets/
$ npx eslint assets/ --fix    //danger
```
