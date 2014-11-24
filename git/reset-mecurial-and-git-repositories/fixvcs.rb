#! /usr/bin/env ruby

Dir.glob("~/Projects/**").each do |dir|
  next unless File.directory?(dir)

  is_hg  = File.directory?(File.join(dir, '.hg'))
  is_git = File.directory?(File.join(dir, '.git'))

  puts "Fixing " + File.basename(dir)
  Dir.chdir(dir)

  if is_hg
    system "hg revert --all --no-backup"
    system "hg pull -u"
  elsif is_git
    system "git reset --hard"
    system "git pull"
  else
    puts "Skipped"
  end
end