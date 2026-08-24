import json
from pathlib import Path
d = json.loads(Path("graphify-out/.graphify_detect.json").read_text(encoding="utf-8"))
print("total_files:", d.get("total_files"))
print("total_words:", d.get("total_words"))
for k, v in d.get("files", {}).items():
    if v: print("  " + k + ": " + str(len(v)))
sk = d.get("skipped_sensitive") or []
print("skipped_sensitive:", len(sk))
