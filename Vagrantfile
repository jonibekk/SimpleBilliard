# -*- mode: ruby -*-
# vi: set ft=ruby :
 
Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  src_dir = './'
  doc_root = '/vagrant_data/app/webroot'
  app_root = '/vagrant_data/'
  # IPアドレスは各アプリ毎に置き換える。(同じIPにしていると他とかぶって面倒)
  config.vm.network "private_network", ip: "192.168.50.4"

  config.vm.provider :virtualbox do |vb|
    # 仮想OSに割り当てるメモリ量
    vb.customize ["modifyvm", :id, "--memory", "1024"]

    # 仮想OSに割り当てるCPUのコア数
    vb.customize ["modifyvm", :id, "--cpus", "1"]
  end

  config.vm.synced_folder src_dir, "/vagrant_data", :create => true, :owner=> 'vagrant', :group=>'www-data', :mount_options => ['dmode=775,fmode=775']
  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = "cookbooks"
    chef.add_recipe "apt"
    chef.add_recipe "php_nginx"
    chef.add_recipe "local_db"
    chef.add_recipe "local_etc"
    chef.add_recipe "deploy_cake_local"
    chef.json = {doc_root: doc_root,app_root: app_root}
  end
 
end
