#!/usr/bin/env bash

password="root"

echo PURGE | sudo debconf-communicate mysql-community-server
sudo apt purge mysql-client mysql-server

sudo debconf-set-selections <<< "mysql-community-server mysql-community-server/root-pass password $password"
sudo debconf-set-selections <<< "mysql-community-server mysql-community-server/re-root-pass password $password"
sudo debconf-set-selections <<< "mysql-community-server mysql-server/default-auth-override select Use Legacy Authentication Method (Retain MySQL 5.x Compatibility)"

sudo apt install -y dirmngr
sudo apt-key adv --keyserver pool.sks-keyservers.net --recv-keys 5072E1F5
echo "deb http://repo.mysql.com/apt/ubuntu $(lsb_release -sc) mysql-8.0" | sudo tee /etc/apt/sources.list.d/mysql80.list
sudo apt-get update

export DEBIAN_FRONTEND="noninteractive" 
sudo apt-get install mysql-server