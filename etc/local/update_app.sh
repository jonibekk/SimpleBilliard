#!/bin/bash

red="\033[31m"  #赤
gre="\033[32m"  #緑
whit="\033[1;37m"   #ホワイト
blu="\033[34m"  #青

TIME_A=`date +%s`

echo "${blu}### Update Application. ###${whit}"
echo "Please wait a minute..."
echo "*** Updating all git submodules."
git submodule update --init --recursive || { echo "${red}*** Error!!${whit}" ; exit 1 ; }
echo "${gre}*** Done.${whit}"
echo "*** git fetch"
git fetch || { echo "${red}*** Error!!${whit}" ; exit 1 ; }
echo "${gre}*** Done.${whit}"
echo "*** git pull"
git pull || { echo "${red}*** Error!!${whit}" ; exit 1 ; }
echo "${gre}*** Done.${whit}"
echo "*** Updating an environment by chef.(include DB schema, using library and more.)"
vagrant provision || { echo "${red}*** Error!!${whit}" ; exit 1 ; }

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
