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
- I assume that you have install requirement properly, different OS will have different way to install it.
- Clone this repository
```
git clone https://github.com/uisyudha/ImagesEncryptionWeb-Based.git
```
- Change to branch master
```
git checkout master
```
- You can check another version if you want
```
master  : latest updated
2.1     : Optimize Server Sent Event and fix datatable wraping problem
2.0     : Change status with progress bar
1.0     : Simple implementation without SSE
```
- In your server run the resque worker
```
nohup php resque.php >> /path/to/your/logfile.log 2>&1 &
```
- [Demo Visit This Link](http://35.197.134.12/images-grain/)