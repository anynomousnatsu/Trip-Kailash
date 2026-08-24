import json
from graphify.cache import check_semantic_cache
from pathlib import Path
detect = json.loads(Path("graphify-out/.graphify_detect.json").read_text(encoding="utf-8"))
all_files = [f for files in detect["files"].values() for f in files]
cached_nodes, cached_edges, cached_hyper, uncached = check_semantic_cache(all_files)
if cached_nodes or cached_edges or cached_hyper:
    Path("graphify-out/.graphify_cached.json").write_text(json.dumps({"nodes":cached_nodes,"edges":cached_edges,"hyperedges":cached_hyper}, ensure_ascii=False), encoding="utf-8")
Path("graphify-out/.graphify_uncached.txt").write_text("\n".join(uncached), encoding="utf-8")
print("Cache: " + str(len(all_files)-len(uncached)) + " hit, " + str(len(uncached)) + " need extraction")
