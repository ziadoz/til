sudo useradd -m -d /home/<username> -s /bin/bash -k /etc/skel -g <primarygroup> -G sudo <username>
sudo passwd <username>
sudo deluser <username>
chsh -s /bin/bash <username>