# Fix OSX battery draining on sleep due to wifi activity
Install [SleepWatcher](http://www.bernhard-baehr.de/) using [Homebrew](http://brew.sh/): 
```
sudo chown -R $(whoami) /usr/local
brew update
brew install sleepwatcher
```

Start the SleepWatcher service: 
```
sudo brew services start sleepwatcher
```

Create a Sleep and Wakeup file in your home directory:
```
touch ~/.sleep && chmod 744 ~/.sleep
touch ~/.wakeup  && chmod 744 ~/.wakeup
```

Put the following script into `~/.sleep` to disable the wifi adapter:
```
#!/bin/bash
/usr/sbin/networksetup setairportpower en0 off
touch /tmp/sleep.log
cat > /tmp/sleep.log
echo "Sleeping: `date`" >> /tmp/sleep.log
echo `/usr/sbin/networksetup getairportpower en0` >> /tmp/sleep.log
```

Put the following script into `~/.wakeup` to re-enable the wifi adapter:
```
#!/bin/bash
sleep 10
/usr/sbin/networksetup setairportpower en0 on
echo "Waking Up: `date`" >> /tmp/sleep.log
echo `/usr/sbin/networksetup getairportpower en0` >> /tmp/sleep.log
```

You can see the most recent sleep/wake cycle in the `/tmp/sleep.log` file: 

```
cat /tmp/sleep.log
```

## Notes
```
pmset -g log
pmset -g assertions
pmset -g sched
```

```
syslog | grep -i "Wake reason"
<Notice>: Wake reason: ARPT (Network)
<Notice>: ARPT: 255862.475269: ARPT: Wake Reason: Wake on Scan offload; Disconnect reason: Unknown
<Notice>: ARPT: 255862.601660: ARPT: Wake Reason: Wake on Scan offload
```

## Links 
- http://www.bernhard-baehr.de/
- https://kevindekoninck.com/portfolio-item/pleasesleep/
- http://osxdaily.com/2015/06/02/change-dns-command-line-mac-os-x/
- http://osxdaily.com/2014/09/03/list-all-network-hardware-from-the-command-line-in-os-x/