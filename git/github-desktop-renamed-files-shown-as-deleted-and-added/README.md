# GitHub Desktop Shows Renamed Files as Deleted + Added

GitHub Desktop may show a renamed file as two separate changes: the original file deleted and the new file added, rather than a single rename.

This happens because git detects renames by comparing file contents between the index (staging area) and the working tree. If the renamed file hasn't been staged yet, git only sees that the old path is gone and a new path exists, so it reports them as separate delete and add operations.

Running `git add -A` stages all changes, including the rename, which gives git enough information to detect it as a rename based on content similarity.

```bash
git add -A
```

After staging, GitHub Desktop will correctly show the operation as a rename.
