document.addEventListener('DOMContentLoaded', () => {
    const paginationLinks = document.querySelectorAll('.page-link');

    paginationLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le rechargement de la page
            const page = this.getAttribute('data-page');
            loadArticles(page); // Appel de la fonction pour charger les articles
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

                // Remplacement des articles
                const newArticles = doc.querySelector('.container-article-cards').innerHTML;
                document.querySelector('.container-article-cards').innerHTML = newArticles;

                // Remplacement de la pagination
                const newPagination = doc.querySelector('#pagination').innerHTML;
                document.querySelector('#pagination').innerHTML = newPagination;

                // Réappliquer les événements aux nouveaux liens de pagination
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
