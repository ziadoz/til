#!/usr/bin/env ruby

# Clone Github Gists.
# Usage: ruby clone-gists.rb username /clone/path

user, dir = ARGV
dir = File.expand_path(dir.to_s)

unless File.directory?(dir)
  require 'fileutils'
  FileUtils.mkdir(dir)
end

abort "Please enter a Github username." if user.nil?
abort "Please enter a valid directory to clone gists in." if dir.nil?

require "uri"
require "net/https"
uri = URI.parse("https://api.github.com/users/#{user}/gists")
http = Net::HTTP.new(uri.host, uri.port)
http.use_ssl = true
request = Net::HTTP::Get.new(uri.request_uri)
response = http.request(request)

require "json"
gists = JSON.parse(response.body)

require "shellwords"
gists.each do |gist|
  next unless gist['public']

  clone_dir = File.join(dir, gist['id'])
  clone_url = Shellwords.escape(gist['git_pull_url'])

  if File.directory?(clone_dir)
    cmd = "cd #{clone_dir} && git pull #{clone_url}"
  else
    cmd = "git clone #{clone_url} #{clone_dir}"
  end

  system(cmd)
end