#! /usr/bin/env ruby

# Mercurial Update
# File: ~/.web/hgupdate.rb

$VERBOSE = nil

VHOST_DIRECTORY = ENV['WEB_DIR'] ||= "/var/www/vhosts"
DIR_PATTERN     = File.join(VHOST_DIRECTORY, "/**")

require "shellwords"
Dir.glob(DIR_PATTERN).each do |dir|
  hg = File.join(dir, ".hg")
  dir = Shellwords.escape(dir)
  system("cd #{dir} && hg pull -u") if File.exists?(hg)
end