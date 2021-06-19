///test
const watchlist = document.querySelector('#watchlist');
watchlist.addEventListener('click', addToWatchlist);

function addToWatchlist(event) {
    event.preventDefault();
    let watchlistLink = event.currentTarget;
    let link = watchlistLink.href;
    fetch(link)
        .then((res) => res.json())
        .then(function (res) {
            let watchlistIcon = watchlistLink.firstElementChild;
            if (res.isInWatchlist) {
                watchlistIcon.classList.remove('bi-heart');
                watchlistIcon.classList.add('bi-heart-fill');
            } else {
                watchlistIcon.classList.remove('bi-heart-fill');
                watchlistIcon.classList.add('bi-heart');
            }
        });
}