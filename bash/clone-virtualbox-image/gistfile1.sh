# Clear Ubuntu image network rules before cloning.
sudo rm /etc/udev/rules.d/70-persistent-net.rules

# Clone a VirtualBox disk image.
VBoxManage clonehd /path/to/original.vdi /path/to/clone.vdi