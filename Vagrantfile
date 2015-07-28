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

Vagrant.configure('2') do |config|
    if Vagrant.has_plugin?('vagrant-cachier')
        config.cache.auto_detect = true
        config.cache.scope = :box

    end

    if Vagrant.has_plugin?('vagrant-omnibus')
        config.omnibus.chef_version = '11.4.4'
    end

    config.vm.define 'default', primary: true do |default|
        default.vm.box = 'hashicorp/precise32'
        # IPアドレスは各アプリ毎に置き換える。(同じIPにしていると他とかぶって面倒)
        default.vm.network 'private_network', ip: '192.168.50.4'

        default.vm.provider :virtualbox do |vb|
            vb.memory = 1024
            vb.cpus = 1
        end
        src_dir = './'
        doc_root = '/vagrant_data/app/webroot'
        app_root = '/vagrant_data/'
        default.vm.synced_folder src_dir, '/vagrant_data', create: true, owner: 'vagrant', group: 'www-data', mount_options: ['dmode=775,fmode=775']

        default.vm.provision :chef_solo do |chef|
            chef.cookbooks_path = 'cookbooks'
            chef.add_recipe 'apt'
            chef.add_recipe 'php_nginx'
            chef.add_recipe 'redisio'
            chef.add_recipe 'redisio::enable'
            chef.add_recipe 'local_db'
            chef.add_recipe 'local_etc'
            chef.add_recipe 'deploy_cake_local'
            chef.json = { doc_root: doc_root, app_root: app_root, php5: { session_secure: 'Off' } }
        end
        if Vagrant.has_plugin?('vagrant-triggers')
            default.trigger.after [:reload, :halt], stdout: true do
                `rm .vagrant/machines/default/virtualbox/synced_folders`
                `pkill vagrant-notify-server`
            end
        end
    end

    config.vm.define 'ec2', autostart: false do |ec2|
        ec2.vm.box = 'aws-dummy'
        ec2.vm.box_url = 'https://github.com/mitchellh/vagrant-aws/raw/master/dummy.box'
        ec2.vm.provider :aws do |aws, override|
            aws.access_key_id = ENV['AWS_ACCESS_KEY']
            aws.secret_access_key = ENV['AWS_SECRET_ACCESS_KEY']
            aws.keypair_name = 'isao-goalous-opsworks'
            aws.instance_type = 'c3.large'
            aws.region = 'ap-northeast-1'
            aws.availability_zone = 'ap-northeast-1a'
            aws.ami = 'ami-01500300'
            aws.associate_public_ip = true
            aws.security_groups = ['sg-0722b862', 'sg-9a1f7aff']
            aws.subnet_id = 'subnet-ad19adda'
            aws.tags = {
                'Name' => 'vnc server for dev'
            }
            override.ssh.username = 'ubuntu'
            override.ssh.private_key_path = '~/.ssh/isao-goalous-opsworks.pem'
        end

        doc_root = '/vagrant/app/webroot'
        app_root = '/vagrant/'

        ec2.vm.provision :chef_solo do |chef|
            chef.cookbooks_path = 'cookbooks'
            chef.add_recipe 'apt'
            chef.add_recipe 'php_nginx'
            chef.add_recipe 'redisio'
            chef.add_recipe 'redisio::enable'
            chef.add_recipe 'local_db'
            chef.add_recipe 'local_etc'
            chef.add_recipe 'deploy_cake_local'
            chef.json = {
                doc_root: doc_root,
                app_root: app_root,
                tmp_dir_owner: 'www-data',
                tmp_dir_group: 'www-data',
                php5: { session_secure: 'Off' },
                bash_profile: {
                    owner: 'ubuntu',
                    group: 'ubuntu',
                    dir: 'vagrant'
                }
            }
        end
        if Vagrant.has_plugin?('vagrant-triggers')
            ec2.trigger.after [:reload, :halt], stdout: true do
                `rm .vagrant/machines/ec2/aws/synced_folders`
                `pkill vagrant-notify-server`
            end
        end
    end
end
