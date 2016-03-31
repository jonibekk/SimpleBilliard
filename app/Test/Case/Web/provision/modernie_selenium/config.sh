# Config; See readme for details.
java_exe="jre-windows-i586.exe"
firefox_exe="firefox.exe"
chrome_exe="chrome.exe"
selenium_jar="selenium-server-standalone.jar"

if [ $(uname) == "Darwin" ]
then
  # This makes sense on a mac
  nic_bridge="en0"
else
  # This works on Ubuntu
  nic_bridge="eth0"
fi

vm_path="VMs/"
vm_mem="768"
vm_mem_xp="512"
deuac_iso="deuac.iso"
current_path=$(cd $(dirname $0); pwd)
tools_path="${current_path}/Tools/"
selenium_path="${current_path}/Tools/selenium_conf/"
ie_cache_reg="ie_disablecache.reg"
ie_protectedmode_reg="ie_protectedmode.reg"
log_path=""
vbox_user="${USER}"
mailto="root@example.com"
create_snapshot=False
