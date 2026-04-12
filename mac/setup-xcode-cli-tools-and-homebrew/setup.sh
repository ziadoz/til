# Install XCode.
# Install XCode CLI Tools.

# Select XCode.
xcode-select -switch /Applications/Xcode.app/Contents/Developer

# Install Auto Tools.
brew install autoconf automake

# Symlink iOS Simulator.
ln -s /Applications/Xcode.app/Contents/Developer/Platforms/iPhoneSimulator.platform/Developer/Applications/iPhone\ Simulator.app /Applications/iPhone\ Simulator.app

# Fix HomeBrew Permissions.
sudo chown -R $(whoami) /usr/local

# Restore MySQL Data Permissions.
sudo chown -R _mysql:_mysql /usr/local/mysql/data/