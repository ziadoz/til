#!/usr/bin/env bash
# @see: https://superuser.com/questions/370388/simple-built-in-way-to-encrypt-and-decrypt-a-file-on-a-mac-via-command-line
# @see: https://askubuntu.com/questions/1093591/how-should-i-change-encryption-according-to-warning-deprecated-key-derivat

alias decrypt="openssl enc -d -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -in $1 -out $2"
alias encrypt="openssl enc -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -salt -in $1 -out $2"