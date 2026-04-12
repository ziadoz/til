#!/usr/bin/env python3
"""Generate README.md from the til directory structure.

For each topic directory, lists every entry with its title (from the git
commit message) and the date it was first committed. Run this after adding
new entries to keep the README in sync.

Usage: python3 generate_readme.py
"""

import re
import subprocess
from pathlib import Path

ROOT = Path(__file__).parent
README = ROOT / "README.md"
SKIP_DIRS = {".git"}
SKIP_FILES = {"generate_readme.py", "README.md", ".gitignore"}


def git(*args):
    result = subprocess.run(
        ["git"] + list(args),
        cwd=ROOT, capture_output=True, text=True, check=True
    )
    return result.stdout.strip()


def entry_date(path):
    """Date of the first commit that touched this path."""
    log = git("log", "--diff-filter=A", "--follow", "--format=%as", "--", str(path))
    return log.splitlines()[-1] if log else ""


def entry_title(path):
    """Title from the commit message, falling back to de-slugified dir name."""
    log = git("log", "--diff-filter=A", "--follow", "--format=%s", "--", str(path))
    msg = log.splitlines()[-1] if log else ""
    # Commit messages are "til(topic): Title" or "refactor: ..."
    match = re.match(r"til\([^)]+\):\s*(.+)", msg)
    if match:
        return match.group(1).strip()
    # Fall back to de-slugifying the directory name
    return path.name.replace("-", " ").title()


def topic_title(name):
    overrides = {
        "html-css": "HTML & CSS",
        "ai-generated": "AI Generated",
        "php": "PHP",
        "javascript": "JavaScript",
        "go": "Go",
        "sql": "SQL",
        "mac": "Mac",
        "docker": "Docker",
    }
    return overrides.get(name, name.replace("-", " ").title())


topics = sorted(
    d for d in ROOT.iterdir()
    if d.is_dir() and d.name not in SKIP_DIRS
)

all_entries = []
topic_sections = []

for topic_dir in topics:
    entries = sorted(
        e for e in topic_dir.iterdir()
        if e.is_dir()
    )
    if not entries:
        continue

    rows = []
    for entry in entries:
        # Use the first file inside the entry dir as the tracked path for dates
        files = sorted(f for f in entry.rglob("*") if f.is_file())
        if not files:
            continue
        date = entry_date(files[0].relative_to(ROOT))
        title = entry_title(files[0].relative_to(ROOT))
        rel = entry.relative_to(ROOT)
        rows.append((date, title, rel))
        all_entries.append((date, title, rel, topic_dir.name))

    rows.sort(key=lambda r: r[0], reverse=True)

    lines = [f"## {topic_title(topic_dir.name)}\n"]
    for date, title, rel in rows:
        lines.append(f"- [{title}]({rel}/) — {date}")
    topic_sections.append("\n".join(lines))

total = len(all_entries)
topic_count = len(topic_sections)

header = f"""\
# TIL

> Things I've picked up, figured out, or found useful. Inspired by [simonw/til](https://github.com/simonw/til).

{total} entries across {topic_count} topics.

## Topics

{chr(10).join(f"- [{topic_title(t.name)}](#{t.name.replace('-', '-')}) ({sum(1 for e in t.iterdir() if e.is_dir())})" for t in topics if any(e.is_dir() for e in t.iterdir()))}
"""

body = "\n\n".join(topic_sections)

README.write_text(f"{header}\n{body}\n")
print(f"README.md written: {total} entries across {topic_count} topics.")
