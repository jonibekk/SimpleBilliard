#!/bin/bash

red="\033[31m"  #赤
gre="\033[32m"  #緑
whit="\033[1;37m"   #ホワイト
blu="\033[34m"  #青

TIME_A=`date +%s`

error_exit(){ echo "${red}*** Error!!${whit}" ; exit 1 ; }

echo "${blu}### Reset Dev DB. ###${whit}"
echo "Please wait a minute..."
echo "*** Delete and Recreate DB tables."
echo -ne '\ny\n' | ./Console/cake schema create || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Import Default Test Data."
./Console/cake data local_test_import || error_exit
echo "${gre}*** Done.${whit}"

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

