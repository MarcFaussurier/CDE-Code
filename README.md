**Compile PHP ZTS with phpbrew on UNIX** (workerman will use swoole ev loop) : 



curl -L -O https://github.com/phpbrew/phpbrew/raw/master/phpbrew
chmod +x phpbrew

# Move phpbrew to somewhere can be found by your $PATH
sudo mv phpbrew /usr/local/bin/phpbrew
phpbrew init

# I assume you're using bash
echo "[[ -e ~/.phpbrew/bashrc ]] && source ~/.phpbrew/bashrc" >> ~/.bashrc

# For the first-time installation, you don't have phpbrew shell function yet.
source ~/.phpbrew/bashrc

# Fetch the release list from official php site...
phpbrew update

`phpbrew install php-7.3.3 +openssl='/usr/local/opt/openssl/' -- --enable-maintainer-zts --with-curl=/usr/local/
` 
`phpbrew switch 7.3.3`


` phpbrew ext install ctypes`

` phpbrew ext install json`

`phpbrew ext install hash
`

` phpbrew ext install pthreads`

` phpbrew ext install swoole`

**Install PHP ZTS on windows** using  Jan-E builds on Apache Louange (workerman will use default ev loop)  : 

https://www.apachelounge.com/viewtopic.php?t=6359

Download & enabled pthreads dll in php . ini