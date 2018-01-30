# Images Encryption Web-Based PHP
Images Encryption using Grain Cipher

## Requirement
- PHP 7
- [Redis](https://redis.io)
- Sqlite3
- [php-resque](https://github.com/chrisboulton/php-resque)
- PHP GD extension
- PHP PCNTL extension

## Instalation
- I assume that you have install requirement
- In your server run
```
nohup php resque.php >> /path/to/your/logfile.log 2>&1 &
```
- [Demo Visit This Link](http://35.197.134.12/images-grain/)