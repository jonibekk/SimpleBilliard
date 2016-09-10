#!/bin/bash

red="\033[1;31m"  #赤
gre="\033[1;32m"  #緑
whit="\033[0m"   #ホワイト
blu="\033[1;34m"  #青

TIME_A=`date +%s`
CURRENT=$(cd $(dirname $0) && pwd)

error_exit(){ echo "${red}*** Error!!${whit}" ; exit 1 ; }

echo "${blu}### Update Application. ###${whit}"
echo "Please wait a minute..."
echo "*** Updating an environment by chef.(include DB schema, using library and more.)"
cd /vagrant/
composer install || error_exit
/vagrant/app/Console/cake migrations.migration run all || error_exit
/vagrant/app/Console/cake remove_cache || error_exit
npm install --no-bin-links || error_exit
grunt chef || error_exit
tmp_dir='/vagrant/app/tmp'
if [ ! -e $tmp_dir ]; then
    mkdir $tmp_dir
    chmod 777 $tmp_dir
fi

sudo service php5-fpm reload

cd $CURRENT

TIME_B=`date +%s`   #B
PT=`expr ${TIME_B} - ${TIME_A}`
H=`expr ${PT} / 3600`
PT=`expr ${PT} % 3600`
M=`expr ${PT} / 60`
S=`expr ${PT} % 60`

echo "${gre}*** All Done!${whit}"
echo "*****************************"
printf "Total Time: %02d:%02d:%02d\n" ${H} ${M} ${S}
echo "*****************************"
echo ""
echo "${blu}Enjoy Development!${whit}"
cat << 'EOF'


 ██████╗  ██████╗  █████╗ ██╗      ██████╗ ██╗   ██╗███████╗
██╔════╝ ██╔═══██╗██╔══██╗██║     ██╔═══██╗██║   ██║██╔════╝
██║  ███╗██║   ██║███████║██║     ██║   ██║██║   ██║███████╗
██║   ██║██║   ██║██╔══██║██║     ██║   ██║██║   ██║╚════██║
╚██████╔╝╚██████╔╝██║  ██║███████╗╚██████╔╝╚██████╔╝███████║
 ╚═════╝  ╚═════╝ ╚═╝  ╚═╝╚══════╝ ╚═════╝  ╚═════╝ ╚══════╝


EOF
