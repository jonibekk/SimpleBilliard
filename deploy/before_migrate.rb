Chef::Log.logger.info '*********** before_migrate ***********'
node[:deploy].each do |application,deploy|
  deploy.each do |key,value|
    Chef::Log.debug("deploy[:#{key}] = '#{value}'")
  end
end
application = params[:app]
Chef::Log.debug("************ path")
Chef::Log.debug("#{node[:deploy][application][:deploy_to]}")
