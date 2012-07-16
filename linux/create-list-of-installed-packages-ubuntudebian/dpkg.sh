# See: http://ubuntuforums.org/showthread.php?t=261366

# Make list of installed software.
dpkg --get-selections > installed-software

# Install software on another machine.
dpkg --set-selections < installed-software
sudo apt-get install dselect
dselect