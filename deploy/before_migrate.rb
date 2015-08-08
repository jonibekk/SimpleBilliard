Chef::Log.logger.info '*********** before_migrate ***********'
node[:deploy].each do |application,deploy|
  deploy.each do |key,value|
    Chef::Log.debug("deploy[:#{key}] = '#{value}'")
  end
end
