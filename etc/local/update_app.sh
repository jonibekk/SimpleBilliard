#!/bin/bash

TIME_A=`date +%s`

echo "### Update Application. ###"
echo "Please wait a minute..."
echo "*** Updating all git submodules."
git submodule update --init --recursive
echo "*** Done."
echo "*** git fetch"
git fetch
echo "*** Done."
echo "*** git pull"
git pull
echo "*** Done."
echo "*** Updating an environment by chef.(include DB schema, using library and more.)"
vagrant provision

TIME_B=`date +%s`   #B
PT=`expr ${TIME_B} - ${TIME_A}`
H=`expr ${PT} / 3600`
PT=`expr ${PT} % 3600`
M=`expr ${PT} / 60`
S=`expr ${PT} % 60`

echo "*** All Done!"
echo "*****************************"
printf "Total Time: %02d:%02d:%02d\n" ${H} ${M} ${S}
echo "*****************************"
echo ""
echo "Enjoy Development!"
cat << EOF


 ██████╗  ██████╗  █████╗ ██╗      ██████╗ ██╗   ██╗███████╗
██╔════╝ ██╔═══██╗██╔══██╗██║     ██╔═══██╗██║   ██║██╔════╝
██║  ███╗██║   ██║███████║██║     ██║   ██║██║   ██║███████╗
██║   ██║██║   ██║██╔══██║██║     ██║   ██║██║   ██║╚════██║
╚██████╔╝╚██████╔╝██║  ██║███████╗╚██████╔╝╚██████╔╝███████║
 ╚═════╝  ╚═════╝ ╚═╝  ╚═╝╚══════╝ ╚═════╝  ╚═════╝ ╚══════╝


EOF
