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
    s3_file "#{release_path}/s3_upload.tar.gz" do
      remote_path "/#{node[:deploy][:cake][:assets_s3_bucket]}/s3_upload.tar.gz"
      bucket "goalous-compiled-assets"
      aws_access_key_id ENV["AWSAccessKeyId"]
      aws_secret_access_key ENV["AWSSecretKey"]
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
      cd #{release_path};

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
      # 初回はMaximum call stack size exceededのエラーになるので強制的に再度実行する
      cd #{release_path}; pnpm i --no-bin-links || true && pnpm i --no-bin-links
      EOS
    end

    bash "run gulp build" do
      user 'deploy'
      group 'www-data'
      code <<-EOS
      source /usr/local/nvm/nvm.sh
      cd #{release_path}; gulp build
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
