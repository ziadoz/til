sudo security delete-certificate -c "Homestead homestead Root CA" /Library/Keychains/System.keychain 2> /dev/null
sudo security add-trusted-cert -d -r trustRoot -p ssl -k /Library/Keychains/System.keychain ~/Projects/homestead/ca.homestead.homestead.crt 2> /dev/null

function homestead() {
    ( cd ~/Projects/homestead && vagrant $* )

    if [[ "$1" == "destroy" ]]; then
        sudo security delete-certificate -c "Homestead homestead Root CA" /Library/Keychains/System.keychain 2> /dev/null
    fi

    if [[ "$1" == "up" ]]; then
        sudo security add-trusted-cert -d -r trustRoot -p ssl -k /Library/Keychains/System.keychain ~/Projects/homestead/ca.homestead.homestead.crt 2> /dev/null
    fi
}
