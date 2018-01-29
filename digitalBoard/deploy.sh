#!/bin/sh

sudo cp website/* /var/www/html/digitalBoard
sudo rm /var/www/html/digitalBoard/dbConfig.ini
sudo mv /var/www/html/digitalBoard/dbConfig_PROD.ini /var/www/html/digitalBoard/dbConfig.ini
