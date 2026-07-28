#!/usr/bin/env python3
"""Create a new TIL entry.

Lists existing categories (or lets you add a new one), prompts for the entry
title, scaffolds `<category>/<slug>/README.md`, opens it in $EDITOR, and prints
(and clipboard-copies) the `til(<category>): <Title>` commit message.

Usage: python3 new.py
"""

# import os
import re
import subprocess
from pathlib import Path

ROOT = Path(__file__).parent


def slugify(text):
    """Kebab-case: lowercase, non-alphanumerics to hyphens, trimmed."""
    return re.sub(r"[^a-z0-9]+", "-", text.lower()).strip("-")


def choose_category():
    categories = sorted(d.name for d in ROOT.iterdir() if d.is_dir() and not d.name.startswith("."))

    print("Categories:")
    for i, name in enumerate(categories, 1):
        print(f"  {i:2}) {name}")
    print(f"  {len(categories) + 1:2}) New category")

    choice = input(f"Select a category [1-{len(categories) + 1}]: ").strip()
    if not choice.isdigit() or not 1 <= int(choice) <= len(categories) + 1:
        raise SystemExit("til: invalid choice")

    if int(choice) == len(categories) + 1:
        category = slugify(input("New category name: "))
        if not category:
            raise SystemExit("til: empty category")
        return category
    return categories[int(choice) - 1]


def main():
    category = choose_category()

    title = input("Entry title: ").strip()
    if not title:
        raise SystemExit("til: empty title")

    entry_dir = ROOT / category / slugify(title)
    if entry_dir.exists():
        raise SystemExit(f"til: entry already exists at {entry_dir}")

    entry_dir.mkdir(parents=True)
    readme = entry_dir / "README.md"
    readme.write_text(f'TODO: write up "{title}".\n')

    commit_msg = f"til({category}): {title}"
    print(f"\nCreated {readme}")
    print(f"Commit message: {commit_msg}")

    try:
        subprocess.run(["pbcopy"], input=commit_msg, text=True, check=True)
        print("(commit message copied to clipboard)")
    except (FileNotFoundError, subprocess.CalledProcessError):
        pass

    # subprocess.run([os.environ.get("EDITOR", "nano"), str(readme)])

if __name__ == "__main__":
    main()
