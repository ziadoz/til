# WGet Manual: http://www.gnu.org/software/wget/manual/wget.html
# Link Checker: http://wummel.github.io/linkchecker/
# Archiving URLs: http://www.gwern.net/Archiving%20URLs
# Download Webpage: http://superuser.com/questions/55040/save-a-single-web-page-with-background-images-with-wget
# Output Directory: http://stackoverflow.com/questions/8986139/wget-output-directory-prefix
# WARC Files: http://www.archiveteam.org/index.php?title=Wget_with_WARC_output
# Add -r or --recursive to get the whole website.

wget -T 10 -e robots=off -E -H -k -K -p -nH -nd -P /path/to/dir [URL]
wget --timeout 10 --execute robots=off --adjust-extension --span-hosts --convert-links --backup-converted --page-requisites --random-wait --no-host-directories --no-directories --directory-prefix --mirror --html-extension /path/to/dir [URL]

function dlwebpage() {
    wget -T 10 -e robots=off -E -H -k -K -p -nH -nd -P $2 $1
}

function dlwebsite() {
    wget -T 10 -r -e robots=off -E -H -k -K -p -nH -nd -P $2 $1
}