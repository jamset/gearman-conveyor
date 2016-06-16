### Install PECL

sudo apt-get install php-pear php5-dev

### Libsodium 

http://doc.libsodium.org/installation/index.html 
https://download.libsodium.org/libsodium/releases/

wget https://download.libsodium.org/libsodium/releases/libsodium-1.0.3.tar.gz

tar -xzvf libsodium-1.0.3.tar.gz && cd libsodium-1.0.3

./configure

sudo make && sudo make install

### Libevent

sudo apt-get install libevent-dev

### Libevent.so

sudo pecl install channel://pecl.php.net/libevent-0.1.0

!You should add "extension=libevent.so" to php.ini

### Gearman

In Ubuntu 14.04, python-software-properties were replaced by software-properties-common. 
Step 1: add PPA

sudo apt-get install software-properties-common

sudo add-apt-repository ppa:gearman-developers/ppa

sudo apt-get update

Step 2: Install the Gearman Job Server & Dev Tools, & Perform Upgrade

sudo apt-get install gearman-job-server libgearman-dev

sudo apt-get upgrade

Step 3: Install PECL & Use It to Install Gearman (CLI, Client, Worker)

sudo pecl install gearman

!You should add "extension=gearman.so" to php.ini

## Optional (for Process&Load Management or Publisher Pulsar module)

### ZMQ

sudo apt-get install pkg-config

wget http://download.zeromq.org/zeromq-4.1.3.tar.gz

tar -xzvf zeromq-4.1.3.tar.gz && cd zeromq-4.1.3 

./configure

sudo make && sudo make install

sudo pecl install zmq-beta

!You should add "extension=zmq.so" to php.ini



