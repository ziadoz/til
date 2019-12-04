# Replace [IP], [USER], [KEY] and [PLAYBOOK]
# Comma after IP address is required, otherwise the command won't work.
ansible-playbook -i [IP], --user=[USER] --private-key=[KEY] [PLAYBOOK]