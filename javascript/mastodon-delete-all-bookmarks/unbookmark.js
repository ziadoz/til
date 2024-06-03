// Inspired by Twitter Unfollow All: https://gist.github.com/ziadoz/caa882e75c11be9e8064e570ffec1f5f

(() => {
  const $bookmarkButtons = 'button.status__action-bar__button.bookmark-icon';

  const retry = {
    count: 0,
    limit: 3,
  };

  const scrollToTheBottom = () => window.scrollTo(0, document.body.scrollHeight);
  const retryLimitReached = () => retry.count === retry.limit;
  const addNewRetry = () => retry.count++;

  const sleep = ({ seconds }) =>
    new Promise((proceed) => {
      console.log(`WAITING FOR ${seconds} SECONDS...`);
      setTimeout(proceed, seconds * 1000);
    });

  const unbookmarkAll = async (bookmarkButtons) => {
    console.log(`UNBOOMARKING ${$bookmarkButtons.length} LINKS...`);
    await Promise.all(
      bookmarkButtons.map(async (bookmarkButton) => {
        bookmarkButton && bookmarkButton.click();
        await sleep({ seconds: 1 });
      })
    );
  };

  const nextBatch = async () => {
    scrollToTheBottom();
    await sleep({ seconds: 1 });

    const bookmarkButtons = Array.from(document.querySelectorAll($bookmarkButtons));
    const bookmarkButtonsWereFound = bookmarkButtons.length > 0;

    if (bookmarkButtonsWereFound) {
      await unbookmarkAll(bookmarkButtons);
      await sleep({ seconds: 2 });
      return nextBatch();
    } else {
      addNewRetry();
    }

    if (retryLimitReached()) {
      console.log(`NO BOOKMARKS FOUND, SO I THINK WE'RE DONE`);
      console.log(`RELOAD PAGE AND RE-RUN SCRIPT IF ANY WERE MISSED`);
    } else {
      await sleep({ seconds: 2 });
      return nextBatch();
    }
  };

  nextBatch();
})();