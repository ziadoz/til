import fetch from "node-fetch";

const INSTANCE = "https://phpc.social"; // change to your instance
const TOKEN = "";

// ===== OPTIONS =====
const DELETE_POSTS = true;
const DELETE_BOOSTS = true;
const DELETE_FAVOURITES = true;

const RATE_DELAY = 350;
// ===================

const sleep = ms => new Promise(r => setTimeout(r, ms));

const headers = {
  Authorization: `Bearer ${TOKEN}`,
  "Content-Type": "application/json"
};

async function api(path, options = {}) {
  const res = await fetch(`${INSTANCE}${path}`, { ...options, headers });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`${res.status}: ${text}`);
  }
  return res;
}

async function getJSON(path) {
  const res = await api(path);
  return res.json();
}

async function main() {
  console.log("🔐 Verifying login...");
  const me = await getJSON("/api/v1/accounts/verify_credentials");
  const myId = me.id;
  console.log("✅ Logged in as", me.acct);

  async function deleteStatuses(kind, filter) {
    let max_id = null;
    let total = 0;

    console.log(`\n📦 Fetching ${kind}...`);

    while (true) {
      const url = new URL(`${INSTANCE}/api/v1/accounts/${myId}/statuses`);
      url.searchParams.set("limit", "40");
      if (max_id) url.searchParams.set("max_id", max_id);

      if (filter) {
        for (const [k, v] of Object.entries(filter))
          url.searchParams.set(k, v);
      }

      const res = await fetch(url, { headers });
      const items = await res.json();
      if (!items.length) break;

      for (const item of items) {
        await api(`/api/v1/statuses/${item.id}`, { method: "DELETE" });
        total++;
        process.stdout.write(`🗑️  Deleted ${kind}: ${total}\r`);
        await sleep(RATE_DELAY);
      }

      max_id = items[items.length - 1].id;
    }

    console.log(`\n✔️ Finished ${kind}: ${total} deleted`);
  }

  if (DELETE_POSTS)
    await deleteStatuses("posts & replies", { exclude_reblogs: "true" });

  if (DELETE_BOOSTS)
    await deleteStatuses("boosts", { only_reblogs: "true" });

  if (DELETE_FAVOURITES) {
    let max_id = null;
    let total = 0;
    console.log("\n📦 Fetching favourites...");

    while (true) {
      const url = new URL(`${INSTANCE}/api/v1/favourites`);
      url.searchParams.set("limit", "40");
      if (max_id) url.searchParams.set("max_id", max_id);

      const res = await fetch(url, { headers });
      const favs = await res.json();
      if (!favs.length) break;

      for (const fav of favs) {
        await api(`/api/v1/statuses/${fav.id}/unfavourite`, { method: "POST" });
        total++;
        process.stdout.write(`🗑️  Unfavourited: ${total}\r`);
        await sleep(RATE_DELAY);
      }

      max_id = favs[favs.length - 1].id;
    }

    console.log(`\n✔️ Finished favourites: ${total} removed`);
  }

  console.log("\n🎉 Mastodon cleanup complete!");
}

main().catch(err => {
  console.error("\n❌ Error:", err.message);
});