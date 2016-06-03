all: fetch fetch_vms

# 必要なツールをダウンロード
fetch: Tools/selenium_conf/selenium-server-standalone.jar Tools/deuac.iso Tools/selenium_conf/IEDriverServer.exe Tools/selenium_conf/chromedrive.exe Tools/jre-windows-i586.exe Tools/firefox.exe Tools/chrome.exe

# 仮想マシンを一括ダウンロード
fetch_vms: VMs/IE10\ -\ Win7.ova VMs/IE11\ -\ Win7.ova VMs/IE10\ -\ Win8.ova VMs/IE11\ -\ Win81.ova

# 特定の仮想マシンをダウンロード
fetch_vm_win7_ie10: VMs/IE10\ -\ Win7.ova
fetch_vm_win7_ie11: VMs/IE11\ -\ Win7.ova
fetch_vm_win8_ie10: VMs/IE10\ -\ Win8.ova
fetch_vm_win81_ie11: VMs/IE11\ -\ Win81.ova

VMs/IE11\ -\ Win81.ova:
	curl -O -L "https://az412801.vo.msecnd.net/vhd/VMBuild_20141027/VirtualBox/IE11/Mac/IE11.Win8.1.For.Mac.VirtualBox.zip"
	chmod +x IE11.Win8.1.For.Mac.VirtualBox.zip
	jar xf ./IE11.Win8.1.For.Mac.VirtualBox.zip
	mkdir VMs || true
	mv ./IE11.Win8.1 VMs/IE11.Win8.1
	rm IE11.Win8.1.For.Mac.VirtualBox.zip

VMs/IE10\ -\ Win8.ova:
	curl -O -L "https://az412801.vo.msecnd.net/vhd/VMBuild_20141027/VirtualBox/IE10/Mac/IE10.Win8.For.Mac.VirtualBox.zip"
	chmod +x IE10.Win8.For.Mac.VirtualBox.zip
	jar xf ./IE10.Win8.For.Mac.VirtualBox.zip
	mkdir VMs || true
	mv ./IE10.Win8 VMs/IE11.Win8
	rm IE10.Win8.For.Mac.VirtualBox.zip

VMs/IE11\ -\ Win7.ova:
	curl -O -L "https://az412801.vo.msecnd.net/vhd/VMBuild_20141027/VirtualBox/IE11/Mac/IE11.Win7.For.Mac.VirtualBox.zip"
	chmod +x IE11.Win7.For.Mac.VirtualBox.zip
	jar xf ./IE11.Win7.For.Mac.VirtualBox.zip
	mkdir VMs || true
	mv ./IE11.Win7 VMs/IE11.Win7
	rm IE11.Win7.For.Mac.VirtualBox.zip

VMs/IE10\ -\ Win7.ova:
	curl -O -L "https://az412801.vo.msecnd.net/vhd/VMBuild_20141027/VirtualBox/IE10/Mac/IE10.Win7.For.Mac.VirtualBox.zip"
	chmod +x IE10.Win7.For.Mac.VirtualBox.zip
	jar xf ./IE10.Win7.For.Mac.VirtualBox.zip
	mkdir VMs || true
	mv ./IE10.Win7 VMs/IE10.Win7
	rm IE10.Win7.For.Mac.VirtualBox.zip

Tools/selenium_conf/selenium-server-standalone.jar:
	curl -o Tools/selenium_conf/selenium-server-standalone.jar -L http://selenium-release.storage.googleapis.com/2.44/selenium-server-standalone-2.44.0.jar

Tools/deuac.iso:
	curl -o Tools/deuac.iso -L https://github.com/tka/SeleniumBox/blob/master/deuac.iso?raw=true

Tools/selenium_conf/IEDriverServer.exe:
	curl -o Tools/selenium_conf/IEDriverServer.zip -L http://selenium-release.storage.googleapis.com/2.44/IEDriverServer_Win32_2.44.0.zip
	#curl -o Tools/selenium_conf/IEDriverServer.zip -L http://selenium-release.storage.googleapis.com/2.44/IEDriverServer_x64_2.44.0.zip
	cd Tools/selenium_conf && unzip IEDriverServer.zip

Tools/selenium_conf/chromedrive.exe:
	curl -o Tools/selenium_conf/chromedriver.zip -L http://chromedriver.storage.googleapis.com/2.15/chromedriver_win32.zip
	cd Tools/selenium_conf && unzip chromedriver.zip

Tools/jre-windows-i586.exe:
	curl -j -o Tools/jre-windows-i586.exe -L -O -H "Cookie: oraclelicense=accept-securebackup-cookie" http://download.oracle.com/otn-pub/java/jdk/7u75-b13/jre-7u75-windows-i586.exe

Tools/firefox.exe:
	curl -o $@ -L "https://download.mozilla.org/?product=firefox-34.0.5-SSL&os=win&lang=en-GB"

Tools/chrome.exe:
	curl -o $@ -L "https://dl.google.com/update2/installers/ChromeStandaloneSetup.exe"
