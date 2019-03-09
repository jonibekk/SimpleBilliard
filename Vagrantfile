# -*- mode: ruby -*-
# vi: set ft=ruby :
required_plugins = %w( vagrant-omnibus vagrant-cachier vagrant-triggers vagrant-berkshelf )
required_plugins.each do |plugin|
    unless Vagrant.has_plugin? plugin
        required_plugins.each do |plugin|
            system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
        end
        print "\e[32m\e[1m*** Please rerun `vagrant up`or`vagrant reload`.\e[0m\n"
        exit
    end
end

Vagrant.configure('2') do |config|

    if Vagrant.has_plugin?('vagrant-cachier')
        config.cache.auto_detect = false
        # config.cache.scope = :box
    end

    if Vagrant.has_plugin?('vagrant-omnibus')
        config.omnibus.chef_version = '12.19.36'
    end

    if Vagrant.has_plugin?('vagrant-berkshelf')
        config.berkshelf.enabled = true
        config.berkshelf.berksfile_path = 'cookbooks/Berksfile'
    end

    if Vagrant.has_plugin?('vagrant-triggers')
        config.trigger.after [:reload, :halt], stdout: true do
            `rm .vagrant/machines/default/virtualbox/synced_folders`
            `pkill vagrant-notify-server`
        end
    end

    # config.vm.box = 'ubuntu/trusty64'
    config.vm.box = 'bigplants/ubuntu14_php7_nginx_mysql_redis'
    # IPアドレスは各アプリ毎に置き換える。(同じIPにしていると他とかぶって面倒)
    config.vm.network 'private_network', ip: '192.168.50.4'

    config.vm.provider :virtualbox do |vb|
        vb.memory = 2048
        vb.cpus = 2
        vb.customize ["modifyvm", :id, "--ioapic", "on"]
    end
    src_dir = './'
    doc_root = '/vagrant/app/webroot'
    app_root = '/vagrant/'
    if ( RUBY_PLATFORM.downcase =~ /darwin/ )
      npm_recipe = 'local_yarn'
      config.vm.synced_folder src_dir, '/vagrant', :nfs => true, mount_options: ['actimeo=2']
    else
      npm_recipe = 'local_npm'
      config.vm.synced_folder src_dir, '/vagrant', create: true, owner: 'vagrant', group: 'www-data', mount_options: ['dmode=775,fmode=775']
    end

    config.vm.provision :chef_solo do |chef|
        chef.cookbooks_path = 'cookbooks'
        chef.add_recipe 'apt'
        chef.add_recipe 'php_nginx'
        chef.add_recipe 'redisio'
        chef.add_recipe 'redisio::enable'
        chef.add_recipe 'local_db'
        chef.add_recipe 'local_etc'
        chef.add_recipe npm_recipe
        chef.add_recipe 'deploy_cake_local'
        chef.json = { doc_root: doc_root, app_root: app_root, php: { session_secure: 'Off' }, phpbrew: { php_version: '7.1.25'} }
    end
end
