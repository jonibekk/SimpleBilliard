# -*- mode: ruby -*-
# vi: set ft=ruby :
required_plugins = %w( vagrant-omnibus vagrant-cachier vagrant-triggers )
required_plugins.each do |plugin|
 unless Vagrant.has_plugin? plugin
   required_plugins.each do |plugin|
     system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
   end
   print "\e[32m\e[1m*** Please rerun `vagrant up`or`vagrant reload`.\e[0m\n"
   exit
 end
end

is_aws = false
if ARGV.include?("--provider=aws") then
  is_aws = true
end

Vagrant.configure("2") do |config|
  if is_aws == true then
    config.vm.box = "aws-dummy"
    config.vm.box_url = "https://github.com/mitchellh/vagrant-aws/raw/master/dummy.box"
    config.vm.provider :aws do |aws, override|
      aws.access_key_id = ENV['AWS_ACCESS_KEY']
      aws.secret_access_key = ENV['AWS_SECRET_ACCESS_KEY']
      aws.keypair_name = 'isao-goalous-opsworks'
      aws.instance_type = 'c3.large'
      aws.region = 'ap-northeast-1'
      aws.availability_zone = 'ap-northeast-1a'
      aws.ami = 'ami-01500300'
      aws.associate_public_ip = true
      aws.security_groups = ['sg-0722b862','sg-9a1f7aff']
      aws.subnet_id = 'subnet-ad19adda'
      aws.tags = {
        'Name' => 'vnc server for dev'
      }
      override.ssh.username = 'ubuntu'
      override.ssh.private_key_path = '~/.ssh/isao-goalous-opsworks.pem'
    end
  else
    config.vm.box = "hashicorp/precise32"
    # IPアドレスは各アプリ毎に置き換える。(同じIPにしていると他とかぶって面倒)
    config.vm.network "private_network", ip: "192.168.50.4"

    config.vm.provider :virtualbox do |vb|
      vb.memory = 1024
      vb.cpus = 1
    end
  end

  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.auto_detect = true
    config.cache.scope = :box

  end

  if Vagrant.has_plugin?("vagrant-omnibus")
    config.omnibus.chef_version = '11.4.4'
  end

  if Vagrant.has_plugin?("vagrant-triggers")
    config.trigger.after [:reload, :halt], stdout: true do
      `rm .vagrant/machines/default/virtualbox/synced_folders`
      `rm .vagrant/machines/default/aws/synced_folders`
      `pkill vagrant-notify-server`
    end
  end

  if is_aws == true then
    doc_root = '/vagrant/goalous2/app/webroot'
    app_root = '/vagrant/goalous2/'
  else
    src_dir = './'
    doc_root = '/vagrant_data/app/webroot'
    app_root = '/vagrant_data/'
    config.vm.synced_folder src_dir, "/vagrant_data", :create => true, :owner=> 'vagrant', :group=>'www-data', :mount_options => ['dmode=775,fmode=775']
  end
  
  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = "cookbooks"
    chef.add_recipe "apt"
    chef.add_recipe "php_nginx"
    chef.add_recipe "redisio"
    chef.add_recipe "redisio::enable"
    chef.add_recipe "local_db"
    if is_aws == false then
      chef.add_recipe "local_etc"
    end
    chef.add_recipe "deploy_cake_local"
    chef.json = {doc_root: doc_root,app_root: app_root, php5:{session_secure:"Off"}}
  end

end
