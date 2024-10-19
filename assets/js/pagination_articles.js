document.addEventListener('DOMContentLoaded', () => {
    const paginationLinks = document.querySelectorAll('.page-link');

    paginationLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Preventing the page from reloading
            const page = this.getAttribute('data-page');
            loadArticles(page); // Call to the function that Loads articles
        });
    });

    function loadArticles(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);

        fetch(url)
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');

                // Replacing the articles
                const newArticles = doc.querySelector('.container-article-cards').innerHTML;
                document.querySelector('.container-article-cards').innerHTML = newArticles;

                // Replacing the paging system
                const newPagination = doc.querySelector('#pagination').innerHTML;
                document.querySelector('#pagination').innerHTML = newPagination;

                // Reapplying the events to the new paging links
                const newPaginationLinks = document.querySelectorAll('.page-link');
                newPaginationLinks.forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const page = this.getAttribute('data-page');
                        loadArticles(page);
                    });
                });
            })
            .catch(error => console.error('Erreur lors du chargement des articles :', error));
    }
});
