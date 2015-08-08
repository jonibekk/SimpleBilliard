Chef::Log.logger.info '*********** before_migrate ***********'
Chef::Log.logger.info "#{node[:deploy][application][:deploy_to]}"
