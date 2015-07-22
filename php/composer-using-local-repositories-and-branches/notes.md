# Notes

You can pull in a local project (e.g. for testing changes) using either `vcs` or `path` repository types in Composer: 

## VCS

Put the path to the local project VCS (Git, Hg, SVN etc) in the `url` field.

See: `composer_vcs.json`

## Path

Put the relative path to the local project in the `url` field.

See: `composer_path.json`

## Branch

Prefix the branch name you want to use with `dev-`, so `my-branch` becomes `dev-my-branch`.

# Links

[Install a Local Package with Composer](http://marekkalnik.tumblr.com/post/22929686367/composer-installing-package-from-local-git)

[Using a Branch as a Dependency in Composer](http://www.lornajane.net/posts/2014/use-a-github-branch-as-a-composer-dependency)

[Composer Path Repositories](https://getcomposer.org/doc/05-repositories.md#path)

[Installing a Local Composer Package in your PHP Project](https://aschmelyun.com/blog/installing-a-local-composer-package-in-your-php-project/)