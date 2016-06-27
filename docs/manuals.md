<hr id="db">
## DB接続

![fullscreen_6_16_15__4_49_pm](https://cloud.githubusercontent.com/assets/3040037/8178324/e9e8def2-1447-11e5-99b9-4faab152c632.png)

![data_sources_and_drivers_and_vagrant_-_goalous2_-____repos_goalous2_](https://cloud.githubusercontent.com/assets/3040037/8178252/47ab49d6-1447-11e5-940e-99c1a863f13e.png)

![data_sources_and_drivers_and_vagrant_-_goalous2_-____repos_goalous2_](https://cloud.githubusercontent.com/assets/3040037/8178248/41b0f5d0-1447-11e5-9d77-1dc680b0c0d7.png)

![data_sources_and_drivers_and_vagrant_-_goalous2_-____repos_goalous2_](https://cloud.githubusercontent.com/assets/3040037/8178243/3938ce1e-1447-11e5-9726-4f4cd0435dcd.png)


![vagrant_-_goalous2_-____repos_goalous2__and_1__bigplants_daikis-macbook-pro____repos_source-han-code-jp__zsh_](https://cloud.githubusercontent.com/assets/3040037/8178275/6d2e2cbe-1447-11e5-9eb3-803625a86c3e.png)

<hr id="trouble-shooting">

# Vagrant
## Vagrant + Chef: Error in provision “Shared folders that Chef requires are missing on the virtual machine.”
See below:
http://stackoverflow.com/questions/27975541/vagrant-chef-error-in-provision-shared-folders-that-chef-requires-are-missin
## Error: Connection timeout. Retrying...
See below:
http://kiririmode.hatenablog.jp/entry/20140331/p1
http://blog.shibayu36.org/entry/2013/03/17/175405
## mysql error occurred during vagrant provision
1. backup DB(VM)
   ```
vagrant@precise32:/vagrant_data/app$ cd ~
vagrant@precise32:~$ mysqldump -u root myapp > dump.sql
```

2. remove mysql completely(VM)
   ```
vagrant@precise32:~$ sudo apt-get purge mysql*
vagrant@precise32:~$ sudo rm -rf /etc/mysql /var/lib/mysql
vagrant@precise32:~$ sudo apt-get autoremove
vagrant@precise32:~$ sudo apt-get autoclean
```

3. vagrant provision(Host)

4. restore DB(VM)
   ```
vagrant@precise32:/vagrant_data/app$ cd ~
vagrant@precise32:~$ mysql -u root myapp < dump.sql
```   

# Opsworks
# Travis
# PhpStorm
# Git
