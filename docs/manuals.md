<hr id="db">

# Connect DB(mysql)

![conncetDB_1]()

![conncetDB_2]()

![conncetDB_3]()

![conncetDB_4]()

![conncetDB_5]()

![conncetDB_6]()

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
