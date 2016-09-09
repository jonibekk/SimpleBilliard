# デプロイフックでcake関連のデプロイ処理を行う
require 'json'

file "/tmp/node.json" do
  content JSON.pretty_generate(node)
end

bash "composer install" do
  user 'deploy'
  group 'www-data'
  code <<-EOS
  cd #{release_path}; composer self-update; composer install --no-interaction --no-dev --prefer-dist
  EOS
end


if node[:deploy][:cake].has_key?(:assets_s3_bucket)
    s3_file "/tmp/s3_upload.tar.gz" do
      remote_path "/#{node[:deploy][:cake][:assets_s3_bucket]}/s3_upload.tar.gz"
      bucket "goalous-compiled-assets"
      s3_url "https://s3-ap-northeast-1.amazonaws.com/goalous-compiled-assets"
      owner  "deploy"
      group  "www-data"
      mode   "0644"
      action :create
    end
    bash "extract asset files" do
      user 'deploy'
      group 'www-data'
      code <<-EOS
      cd /tmp
      tar zxvf s3_upload.tar.gz
      cp s3_upload/css/goalous.min.css #{release_path}/app/webroot/css/
      cp s3_upload/js/* #{release_path}/app/webroot/compiled_assets/js/
      EOS
    end
else
    file '/home/deploy/.npmrc' do
      owner 'deploy'
      group 'aws'
      mode '0755'
    end

    bash "pnpm install" do
      user 'deploy'
      group 'www-data'
      code <<-EOS
      source /usr/local/nvm/nvm.sh
      cd #{release_path}
      if ! pnpm i --no-bin-links; then
        rm -rf node_modules
        pnpm i --no-bin-links
      fi
      EOS
    end

    bash "run gulp build" do
      user 'deploy'
      group 'www-data'
      code <<-EOS
      source /usr/local/nvm/nvm.sh
      cd #{release_path}
      if ! gulp build; then
        gulp build
      fi
      EOS
    end
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
template "/home/deploy/.bash_profile" do
  owner "deploy"
  group "www-data"
  mode 0644
  source ".bash_profile"
end
