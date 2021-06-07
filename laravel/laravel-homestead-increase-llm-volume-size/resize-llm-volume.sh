#!/usr/bin/env bash

# @see: https://chasingcode.dev/blog/fix-vagrant-homestead-cant-create-database/
# @see: https://github.com/laravel/homestead/issues/1189#issuecomment-787935903
# @see: https://laracasts.com/discuss/channels/guides/a-guide-to-extending-homestead-storage-capacity
# @see: https://www.thegeekdiary.com/how-to-enable-thin-lvm-automatic-extension/
# @see: https://laracasts.com/discuss/channels/guides/a-guide-to-extending-homestead-storage-capacity
# @see: https://askubuntu.com/questions/433705/dev-mapper-ubuntu-vg-root-is-full

# Default LVM volume size is 64GB, this will add 50% of the available disk space (512GB) to it, making it 256GB:
sudo lvextend -r -l +50%FREE /dev/mapper/homestead--vg-thinpool
sudo lvextend -r -l +100%FREE /dev/mapper/homestead--vg-mysql--master

# The volume size can be tested by using filling it up with a fake file (try 100G and 300G to fill 64GB and 256GB):
sudo fallocate -l 300G /var/lib/mysql/test.txt
sudo rm -rf /var/lib/mysql/test.txt

# List volumes:
sudo fdisk -l
sudo lvdisplay
sudo lsblk -o NAME,FSTYPE,SIZE,MOUNTPOINT,LABEL
sudo parted -l
sudo lvs