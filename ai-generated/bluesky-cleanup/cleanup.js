import { BskyAgent } from "@atproto/api";

const SERVICE = "https://bsky.social";

// ==== YOUR LOGIN INFO ====
const HANDLE = "ziadoz.bsky.social";     // e.g. alice.bsky.social
const PASSWORD = "";  // create in Bluesky settings

// ==== OPTIONS ====
const DELETE_POSTS = true;
const DELETE_LIKES = true;
const DELETE_REPOSTS = true;

const RATE_DELAY = 300; // ms between actions (safe & slow)

// ===========================

const sleep = ms => new Promise(r => setTimeout(r, ms));

async function main() {
  const agent = new BskyAgent({ service: SERVICE });

  console.log("🔐 Logging in...");
  await agent.login({ identifier: HANDLE, password: PASSWORD });
  console.log("✅ Logged in as", agent.session.handle);

  const did = agent.session.did;

  async function deleteCollection(name) {
    let cursor;
    let total = 0;

    console.log(`\n📦 Fetching ${name}...`);

    do {
      const res = await agent.com.atproto.repo.listRecords({
        repo: did,
        collection: name,
        limit: 100,
        cursor
      });

      cursor = res.data.cursor;

      for (const record of res.data.records) {
        const uri = record.uri;
        const rkey = uri.split("/").pop();

        await agent.com.atproto.repo.deleteRecord({
          repo: did,
          collection: name,
          rkey
        });

        total++;
        process.stdout.write(`🗑️  Deleted ${name}: ${total}\r`);
        await sleep(RATE_DELAY);
      }
    } while (cursor);

    console.log(`\n✔️ Finished ${name}: ${total} deleted`);
  }

  if (DELETE_POSTS)
    await deleteCollection("app.bsky.feed.post");

  if (DELETE_LIKES)
    await deleteCollection("app.bsky.feed.like");

  if (DELETE_REPOSTS)
    await deleteCollection("app.bsky.feed.repost");

  console.log("\n🎉 Cleanup complete!");
}

main().catch(err => {
  console.error("❌ Error:", err);
});