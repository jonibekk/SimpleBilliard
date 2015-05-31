#!/bin/bash

red="\033[1;31m"  #赤
gre="\033[1;32m"  #緑
whit="\033[0m"   #ホワイト
blu="\033[1;34m"  #青

TIME_A=`date +%s`

if [ $# -ne 0 ]; then
  DISIT=$1;
else
  DISIT=6;
fi


error_exit(){ echo "${red}*** Error!!${whit}" ; exit 1 ; }

echo "${blu}### Preparing Data For Stress Test. ###${whit}"
echo "Please wait a minute..."
echo "*** Truncate teams."
./Console/cake dummy_data truncate_table -t teams || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into users."
./Console/cake dummy_data -c default -t users -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into emails."
./Console/cake dummy_data -c default -t emails -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into team_members."
./Console/cake dummy_data -c default -t team_members -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into posts."
./Console/cake dummy_data -c default -t posts -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into post_likes."
./Console/cake dummy_data -c default -t post_likes -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into post_reads."
./Console/cake dummy_data -c default -t post_reads -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into comments."
./Console/cake dummy_data -c default -t comments -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into comment_likes."
./Console/cake dummy_data -c default -t comment_likes -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into comment_reads."
./Console/cake dummy_data -c default -t comment_reads -d ${DISIT} || error_exit
echo "${gre}*** Done.${whit}"
echo "*** Insert into action_results."
./Console/cake dummy_data -c default -t action_results -d ${DISIT} || error_exit
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

