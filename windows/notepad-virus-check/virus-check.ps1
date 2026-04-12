# Check for suspicious directories
Test-Path "$env:APPDATA\Bluetooth"
Test-Path "C:\ProgramData\USOShared"

# Check for suspicious services
Get-Service | Where-Object {$_.Name -like "*Bluetooth*"}

# Check registry run keys
Get-ItemProperty "HKCU:\Software\Microsoft\Windows\CurrentVersion\Run"
Get-ItemProperty "HKLM:\Software\Microsoft\Windows\CurrentVersion\Run"
