<hr id="db">

# Connect DB(mysql)
In this case using PhpStorm  

![conncetDB_1](https://user-images.githubusercontent.com/9262490/28098454-53ff2dde-66f1-11e7-911c-726617633078.png)

![conncetDB_2](https://user-images.githubusercontent.com/9262490/28098514-b95b85ce-66f1-11e7-92f4-364fac83c5cf.png)

![conncetDB_3](https://user-images.githubusercontent.com/9262490/28098535-d35da51a-66f1-11e7-9d45-18cbc1f52752.png)

![conncetDB_4](https://user-images.githubusercontent.com/9262490/28098545-e2bc50ec-66f1-11e7-91a7-8e39470ce8da.png)

![conncetDB_5](https://user-images.githubusercontent.com/9262490/28098555-f0efb262-66f1-11e7-8678-1f62161586f3.png)

![conncetDB_6](https://user-images.githubusercontent.com/9262490/28098560-fa92476c-66f1-11e7-8903-8837c9e2f1a9.png)

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
