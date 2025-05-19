import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['card'];
    filter(event) {
        // Ignore si c'est une touche autre que Enter pour le champ de recherche
        if (event.type === 'keyup' && event.key !== 'Enter' && event.target.id === 'search') {
            return;
        }

        const formData = new FormData();
        const search = document.getElementById('search').value;
        const region = document.getElementById('region').value;
        const reseau = document.getElementById('reseau').value;
        const isActif = document.querySelector('input[name="is_actif"]:checked')?.value;

        if (search) formData.append('search', search);
        if (region) formData.append('region', region);
        if (reseau) formData.append('reseau', reseau);
        if (isActif) formData.append('is_actif', isActif);

        const params = new URLSearchParams(formData);
        Turbo.visit(`${window.location.pathname}?${params.toString()}`, { action: 'replace' });
    }

    toggleCard(event) {
        const card = event.currentTarget;
        const isExpanded = card.classList.contains('expanded');

        // Fermer toutes les cartes d'abord
        this.cardTargets.forEach(c => {
            c.classList.remove('expanded');
        });

        // Ouvrir la carte cliquée si elle n'était pas déjà ouverte
        if (!isExpanded) {
            card.classList.add('expanded');
        }
    }
}