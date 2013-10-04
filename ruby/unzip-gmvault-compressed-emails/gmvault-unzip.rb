#!/usr/bin/env ruby

# GMVault: http://gmvault.org/
# Unzips GMVault compressed emails (.eml.gz).
# Enter an absolute path, e.g., /Users/Username/gmvault-db/db

print 'Enter the directory path containing the zip files: '
path = STDIN.gets.chomp.chomp('/')
path = path + '/**/*.eml.gz'

require 'shellwords'
Dir.glob(path).each do |gzip|
  puts 'Extracting Email: ' + gzip
	system "gunzip #{Shellwords.escape(gzip)}"
end

puts 'Completed!'