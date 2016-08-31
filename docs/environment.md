### Install PECL

```bash
sudo apt-get install php-pear php5-dev (PHP7: php-dev) 
```

### Gearman

```bash
sudo apt-get install gearman libgearman-dev

sudo apt-get upgrade

sudo pecl install gearman
```
!You should add "extension=gearman.so" to php.ini

## Optional 
(for Process&Load Management or Publisher Pulsar modules)

### Libevent
```bash
sudo apt-get install libevent-dev
```
### Libevent.so (PHP7: not needed - not compatible)

```bash
sudo pecl install channel://pecl.php.net/libevent-0.1.0
```
!You should add "extension=libevent.so" to php.ini

### Libsodium 

http://doc.libsodium.org/installation/index.html 
https://download.libsodium.org/libsodium/releases/

```bash
wget https://download.libsodium.org/libsodium/releases/libsodium-1.0.3.tar.gz

tar -xzvf libsodium-1.0.3.tar.gz && cd libsodium-1.0.3

./configure

sudo make && sudo make install
```
### ZMQ

```bash
sudo apt-get install pkg-config

wget http://download.zeromq.org/zeromq-4.1.3.tar.gz

tar -xzvf zeromq-4.1.3.tar.gz && cd zeromq-4.1.3 

./configure

sudo make && sudo make install

sudo pecl install zmq-beta
```
!You should add "extension=zmq.so" to php.ini






