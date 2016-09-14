# Fix AMD HDMI audio lost after monitor standby
The quickest solution to this issue is to disable and then re-enable the `AMD High Definition Audio Device` in the Device Manager. However it's possible to create a handy desktop shortcut: 

Download [DevManView](http://www.nirsoft.net/utils/device_manager_view.html) and put the files in `C:\Program Files\NirSoft`.

Create a file called `Restart AMD Audio.bat` on the desktop with the following contents:

```
"C:\Program Files\NirSoft\DevManView.exe" /disable_enable "AMD High Definition Audio Device"
```

## Links
- http://www.nirsoft.net/utils/device_manager_view.html
- https://community.amd.com/thread/190412?start=105&tstart=0
- http://superuser.com/questions/605972/hdmi-audio-drops-out-when-display-enters-powersave