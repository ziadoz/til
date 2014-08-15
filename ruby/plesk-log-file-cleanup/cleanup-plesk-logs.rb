#! /usr/bin/env ruby

# Cleanup Plesk 10.3 Log Files
# A handy little script to truncate Plesk log files. 
# Useful for when you change the subscription log rotation settings.
# Make sure you backup your log files beforehand.
#
# Usage: 
# ruby cleanup-plesk-logs.rb /var/www/vhosts
# ruby cleanup-plesk-logs.rb /var/www/vhosts -f

site_dir, execute = ARGV
site_dir = File.expand_path(site_dir.to_s)
execute = (execute == '-f')

abort "Please specify the website path." if site_dir.nil?

sites = Dir.glob(File.join(site_dir, '*'))
log_dir = 'statistics/logs'
logs = ['access_log', 'error_log', 'access_log.process', 'error_log.process', 'access_log.processed', 'error_log.processed']

sites.each do |site|
  next unless Dir.exists?(site)
  
  puts File.basename(site) + ': '
  logs.each do |log|
    log = File.join(site, log_dir, log)
    next unless File.exists?(log)

    cmd = 'sudo cp /dev/null ' + log
    result = true
    result = system(cmd) if execute 

    puts cmd + ' -> ' + (result ? 'true' : 'false')
  end
  puts
end