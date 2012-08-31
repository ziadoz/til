#! /usr/bin/env ruby

# Web Udpate
# File: ~/.web/webupdate.rb

$VERBOSE = nil

VHOST_DIRECTORY = ENV['WEB_DIR'] ||= "/var/www/vhosts"
VHOST_PATTERN   = File.join(VHOST_DIRECTORY, "/**/dev.conf")
VHOST_FILE      = File.join(VHOST_DIRECTORY, "vhosts.conf")

confs = Dir.glob(VHOST_PATTERN)
open(VHOST_FILE, "w") do |file|
  includes = confs.map{ |conf| "Include #{conf}" }.join("\n")
  file.write(includes)
end

system("sudo apachectl configtest")
system("sudo apachectl restart")