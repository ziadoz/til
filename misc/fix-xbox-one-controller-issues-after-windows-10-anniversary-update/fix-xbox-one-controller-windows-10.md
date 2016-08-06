# Fix Xbox One Controller on Windows 10 Anniversary

After the Windows 10 Anniversary update Xbox One Controllers may start showing up as two XInput devices in Steam, and button presses occur twice. 

To fix this you need to patch in the `xinputhid.sys` file from an earlier Windows 7 driver.

Note: This fix only works for connecting the controller via USB cable or USB wireless adapter, the new Bluetooth controller when paired still doesn't work.

## Download the Windows 7 Driver

Head to the [Microsoft Catalog](http://catalog.update.microsoft.com/v7/site/Search.aspx?q=xbox+wireless) in Internet Explorer (it won't work in any other browser) and download `Microsoft - Generic Controller - Xbox Wireless Adapter for Windows` version `6.3.9600.16384`.

Make sure you download the right version for your machine (`X86` or `AMD64`). You can check by clicking the package name to view the details.

Add the driver to you basket and checkout. Once you have the file, extract the zip inside the directory. You should see `xinputhid.sys` and `xinputhid.inf` in there.

## Patching Existing Driver

Bring up the Device Manager (Right click on Start), expand `Human Interface Device` and find your Xbox Controller (e.g. `Xbox Wireless Controller`).

Click on the device and go to the Driver tab and click Update Driver. 

Choose Browse my computer for driver software.

Choose let me pick from a list of device drivers on my computer.

Choose on `HID-compliant device` from the list and then click Have Disk.

Browse to where you download the drivers earlier and choose `xinputhid.inf` then hit Ok.

Your controller should work now. Try connecting it via USB, and then reconnecting it by USB wireless adapter.

## Links
- http://www.neogaf.com/forum/showthread.php?t=1258028
- http://forums.xbox.com/xbox_support/xbox_on_windows_support/f/5412/p/2195680/5717060.aspx#5717060
- https://www.youtube.com/watch?v=ZlvLftCqvSo
- http://forums.overclockers.ru/viewtopic.php?p=13615826#p13615826