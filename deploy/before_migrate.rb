# デプロイフックでcake関連のデプロイ処理を行う

bash "composer install" do
  user 'ubuntu'
  group 'www-data'
  code <<-EOS
  cd #{release_path}; composer self-update; composer install --no-interaction --no-dev --prefer-dist
  EOS
end
bash "pnpm install" do
  user 'ubuntu'
  group 'www-data'
  code <<-EOS
  source /usr/local/nvm/nvm.sh
  npm set progress=false
  cd #{release_path}; pnpm i --no-bin-links
  EOS
end

bash "run gulp build" do
  user 'ubuntu'
  group 'www-data'
  code <<-EOS
  source /usr/local/nvm/nvm.sh
  cd #{release_path}; gulp build
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
