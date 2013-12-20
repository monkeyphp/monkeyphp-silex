#
# Cookbook Name:: monkeyphp
# Recipe:: default
#
# Copyright 2013, YOUR_COMPANY_NAME
#
# All rights reserved - Do Not Redistribute
#

include_recipe "apache2"
include_recipe "mysql::server"
include_recipe "mysql::client"

include_recipe "php"
include_recipe "php::module_mysql"
include_recipe "apache2::mod_php5"
include_recipe "mysql::ruby"
include_recipe "varnish::apt_repo"
include_recipe "varnish"
include_recipe "java"
include_recipe "rabbitmq"
include_recipe "rabbitmq::mgmt_console"
include_recipe "elasticsearch"

package "php5-intl" do
  action :install
end

package "php5-curl" do
  action :install
end

package "php-apc" do
  action :install
end

package "memcached" do
  action :install
end

package "php5-memcached" do
  action :install
end

mysql_database node['monkeyphp']['database'] do
    connection ({
        :host     => 'localhost',
        :username => 'root',
        :password => node['mysql']['server_root_password']
    })
    action :create
end

mysql_database_user node['monkeyphp']['db_username'] do
  connection ({
        :host     => 'localhost',
        :username => 'root',
        :password => node['mysql']['server_root_password']
    })
    password node['monkeyphp']['db_password']
    database_name node['monkeyphp']['database']
    privileges [:select,:update,:insert,:create,:delete]
    action :grant
end

web_app "application" do
    server_name node['monkeyphp']['server_name']
    docroot node['monkeyphp']['docroot']
end