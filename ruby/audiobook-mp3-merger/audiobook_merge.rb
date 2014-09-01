#!/usr/bin/env ruby

# Merge Audiobook MP3s
# This script will iterate through directories of audiobooks, 
# combine the MP3 files and output the new file to a destination directory.
# 
# Usage:    ruby audiobook_merge.rb [SRC] [DEST]
# Example:  ruby audiobook_merge.rb /audiobooks /audiobooks/combined

abort "Could not locate the 'cat' binary on your computer." if `which cat`.empty?

src, dest = ARGV
src = File.expand_path(src.to_s)
dest = File.join(src, "combined") if dest.nil?

abort "Please specify a source directory." if src.nil?
abort "Please specify a destination directory." if dest.nil?
 
audiobooks = Dir.glob(File.join(src, "*"))
audiobooks.delete(dest)

if ! File.directory?(dest) && audiobooks.length > 0
  require "fileutils"
  FileUtils.mkdir(dest)
end

require "shellwords"
audiobooks.each do |path|
  file = File.basename(path)
  source = File.join(path, '/')
  output = File.join(dest, file)

  mp3s = Dir.glob(File.join(path, '*.mp3'))
  unless mp3s.length > 0
    puts "Could not find files to combine for '#{file}'"
    next
  end

  if File.exists?(output + '.mp3')
    puts "A combined output file already exists for '#{file}'"
    next
  end

  puts "Combining #{mp3s.length} files into '#{file}.mp3'"
  cmd = "cat " + Shellwords.escape(source) + "*.mp3 > " + Shellwords.escape(output) + '.mp3'
  system(cmd)
end