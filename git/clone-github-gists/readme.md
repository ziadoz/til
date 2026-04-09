# Cloning Github Gists

To clone a gist: 
```
git clone git@gist.github.com:[GIST ID].git
```

If you need to add a remote to an existing repository:
```
git remote add origin git@gist.github.com:[GIST ID].git
git pull origin master --allow-unrelated-histories
git push --set-upstream origin master
```