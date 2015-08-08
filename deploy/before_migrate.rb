# デプロイフックでcake関連のデプロイ処理を行う

bash "composer update" do
  code <<-EOS
  cd #{release_path}; composer self-update; composer update --no-interaction --no-dev --prefer-dist
  EOS
end
bash "npm install" do
  code <<-EOS
  cd #{release_path}; npm install
  EOS
end

bash "run grunt chef" do
  code <<-EOS
  cd #{release_path}; grunt chef
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
  not_if {::File.exists?("#{release_path}/app/tmp")}
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
