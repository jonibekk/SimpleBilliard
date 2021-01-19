# デプロイフックでcake関連のデプロイ処理を行う
require 'json'

file "/tmp/node.json" do
  content JSON.pretty_generate(node)
end

bash "composer install" do
  user 'deploy'
  group 'www-data'
  code <<-EOS
  source /opt/phpbrew/bashrc
  cd #{release_path}; composer self-update 1.10.15; yes | composer install --no-interaction --no-dev --prefer-dist
  EOS
  environment('PHPBREW_ROOT' => '/opt/phpbrew', 'PHPBREW_HOME' => '/opt/phpbrew')
end

execute 'chmod browscap resources' do
  command "chmod 775 -R #{release_path}/Vendor/browscap/browscap-php/src/../resources/"
end

bash 'update browscap' do
  user 'deploy'
  group 'www-data'
  code <<-EOS
  source /opt/phpbrew/bashrc
  cd #{release_path}; Vendor/bin/browscap-php browscap:convert /opt/lite_php_browscap.ini
  EOS
  environment('PHPBREW_ROOT' => '/opt/phpbrew', 'PHPBREW_HOME' => '/opt/phpbrew')
end


file '/home/deploy/.npmrc' do
  owner 'deploy'
  group 'aws'
  mode '0755'
end

bash "yarn install" do
  user 'deploy'
  group 'www-data'
  code <<-EOS
  source /usr/local/nvm/nvm.sh
  export USER='deploy'
  export HOME='/home/deploy'
  cd #{release_path}
  yarn install
  EOS
end

# gulp buildを環境変数によってproduction,developmentを指定で実行するようにする。環境変数はOpsWorks側で設定可能。環境変数の設定についてはEnvironment Variablesを参照-> http://amzn.to/2mdJ4pq
gulp_command = "gulp build"
app = search("aws_opsworks_app").first

if app['environment']['NODE_ENV'] then
  gulp_command = "NODE_ENV=#{app['environment']['NODE_ENV']} gulp build"
end

bash "run gulp build" do
  user 'deploy'
  group 'www-data'
  code <<-EOS
  source /usr/local/nvm/nvm.sh
  export USER='deploy'
  export HOME='/home/deploy'
  cd #{release_path}
  if ! #{gulp_command}; then
    #{gulp_command}
  fi
  EOS
end

bash "ntpdate" do
  user "root"
  code <<-EOS
  /etc/init.d/ntp stop
  ntpdate ntp.ubuntu.com
  /etc/init.d/ntp start
  EOS
end

# tmpディレクトリ作成
directory "#{release_path}/app/tmp" do
  owner 'deploy'
  group 'www-data'
  mode 0777
  action :create
  not_if {::Dir.exists?("#{release_path}/app/tmp")}
end

# 外部API用Keyの定義
template "#{release_path}/app/Config/extra_defines.php" do
  mode 0644
  source "extra_defines.php.erb"
end

# bash_profileの更新
template "/home/ubuntu/.bash_profile" do
  owner "ubuntu"
  group "ubuntu"
  mode 0644
  source ".bash_profile"
end
template "/home/deploy/.bash_profile" do
  owner "deploy"
  group "www-data"
  mode 0644
  source ".bash_profile"
end
# /var/log/goalousディレクトリ作成
directory "/var/log/goalous" do
    owner 'deploy'
    group 'www-data'
    mode 0775
    action :create
    not_if {::Dir.exists?("/var/log/goalous")}
end

# Create empty app log
%w{debug error emergency}.each do |level|
  file "/var/log/goalous/#{level}.log" do
      owner 'deploy'
      group 'www-data'
      mode 0664
      action :create_if_missing
  end
end
