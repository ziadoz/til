# Bash Profile
# File: ~/.bash_profile
# Set $WEB_DIR and $SCRIPTS_DIR to the appropriate directories.

export PATH="./vendor/bin:$PATH"
export EDITOR=nano

export WEB_DIR="/var/www/vhosts"
export SCRIPTS_DIR="~/.web"

alias reloadhosts="dscacheutil -flushcache"
alias reloadbash="source ~/.bash_profile"
alias editbash="subl ~/.bash_profile"

alias webdir="cd $WEB_DIR"
alias webvhosts="subl $WEB_DIR/vhosts.conf"
alias webstart="sudo apachectl start"
alias webstop="sudo apachectl stop"
alias webrestart="sudo apachectl restart"
alias webconfigtest="sudo apachectl configtest"
alias webupdate="ruby $SCRIPTS_DIR/webupdate.rb"
alias webpull="ruby $SCRIPTS_DIR/hgupdate.rb"