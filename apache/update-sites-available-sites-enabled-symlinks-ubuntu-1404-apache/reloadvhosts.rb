#! /usr/bin/env ruby

$VEBOSE = nil

class String
  def colourise(color_code)
  "\e[#{color_code}m#{self}\e[0m"
  end

  def red
    colourise(31)
  end

  def green
    colourise(32)
  end

  def yellow
    colourise(33)
  end
end

require 'pathname'
def get_project_name(path)
  bits = Pathname(path).each_filename.to_a
  bits[3]
end

def get_conf_name(path)
  get_project_name(path).gsub('.', '_') + '.conf'
end

# args = {}
# args[:test] = ARGV.any? { |word| ["-t", "--test"].include?(word) }
# args[:debug] = ARGV.any? { |word| ["-d", "--debug"].include?(word) }

projects_dir = ENV["WEB_DIR"] ||= "/var/www/vhosts"
apache_dir = "/etc/apache2"

is_default = Proc.new do |conf|
  conf = File.basename(conf)
  conf.start_with?("000") || conf.start_with?("default")
end

sites_available = Dir.glob(File.join(apache_dir, "sites-available", "**")).reject(&is_default)
sites_enabled = Dir.glob(File.join(apache_dir, "sites-enabled", "**")).reject(&is_default)
projects = Dir.glob(File.join(projects_dir, "**", "dev.conf"))

require "shellwords"

puts "Cleaning Up Sites Enabled".yellow
sites_enabled.each do |conf|
  system "sudo a2dissite " + Shellwords.escape(File.basename(conf)) + " > /dev/null"
end

puts "Cleaning Up Sites Available".yellow
sites_available.each do |conf|
  system "sudo rm " + Shellwords.escape(conf)
end

puts ("Enabling " + projects.length.to_s + " Sites: ").yellow
projects.each do |conf|
  name = get_conf_name(conf)
  link = File.join(apache_dir, 'sites-available', name)

  system "sudo ln -s " + Shellwords.escape(conf) + " " + Shellwords.escape(link)
  system "sudo a2ensite " + Shellwords.escape(name) + " > /dev/null"
  
  puts " - " + get_project_name(conf)
end

puts "Restarting Apache: ".yellow
system "sudo service apache2 restart"

puts "Completed".green