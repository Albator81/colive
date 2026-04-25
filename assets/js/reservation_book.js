document.addEventListener('DOMContentLoaded', function() {
    const unavailableDates = JSON.parse(document.body.dataset.unavailableDates || '[]');
    const minDate = document.body.dataset.minDate;
    const maxDate = document.body.dataset.maxDate;

    const startInput = document.getElementById('reservation_dateDebut') || document.querySelector('input[name*="dateDebut"]');
    const endInput = document.getElementById('reservation_dateFin') || document.querySelector('input[name*="dateFin"]');

    const boxStart = document.getElementById('box_start');
    const boxEnd = document.getElementById('box_end');
    const textStart = document.getElementById('text_start');
    const textEnd = document.getElementById('text_end');
    const btnClear = document.getElementById('btn_clear');

    // Utilitaires pour basculer les classes Bootstrap (Actif / Inactif)
    function setActiveBox(box, textElem) {
        box.className = 'flex-fill p-3 border border-2 border-primary rounded-3 bg-white shadow-sm';
        textElem.classList.remove('text-muted');
        textElem.classList.add('text-dark');
    }

    function setInactiveBox(box, textElem) {
        box.className = 'flex-fill p-3 border border-2 border-light rounded-3 bg-light shadow-none';
        textElem.classList.remove('text-dark');
        textElem.classList.add('text-muted');
    }

    const fp = flatpickr("#inline_calendar", {
        inline: true,
        mode: "range",
        locale: "fr",
        minDate: minDate,
        maxDate: maxDate,
        disable: unavailableDates,
        dateFormat: "Y-m-d",
        showMonths: window.innerWidth < 768 ? 1 : 2,

        onChange: function(selectedDates, dateStr, instance) {
            // Remplissage Date Arrivée
            if (selectedDates.length > 0) {
                textStart.textContent = instance.formatDate(selectedDates[0], "d M Y");
                if (startInput) startInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
            } else {
                textStart.textContent = "Ajouter une date";
                if (startInput) startInput.value = "";
            }

            // Remplissage Date Départ
            if (selectedDates.length > 1) {
                textEnd.textContent = instance.formatDate(selectedDates[1], "d M Y");
                if (endInput) endInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
            } else {
                textEnd.textContent = "Ajouter une date";
                if (endInput) endInput.value = "";
            }

            // Gestion de l'affichage (Quelle boîte est active visuellement)
            if (selectedDates.length === 0 || selectedDates.length === 2) {
                if(selectedDates.length === 2) {
                    setInactiveBox(boxStart, textStart);
                    setActiveBox(boxEnd, textEnd);
                } else {
                    setActiveBox(boxStart, textStart);
                    setInactiveBox(boxEnd, textEnd);
                }
            } else if (selectedDates.length === 1) {
                setInactiveBox(boxStart, textStart);
                setActiveBox(boxEnd, textEnd);
            }
        }
    });

    // Effacer
    btnClear.addEventListener('click', () => {
        fp.clear();
        setActiveBox(boxStart, textStart);
        setInactiveBox(boxEnd, textEnd);
    });

    // Clic manuel sur la boîte Arrivée
    boxStart.addEventListener('click', () => {
        setActiveBox(boxStart, textStart);
        setInactiveBox(boxEnd, textEnd);
        if(fp.selectedDates.length === 2) {
            fp.clear();
        }
    });

    // Clic manuel sur la boîte Départ
    boxEnd.addEventListener('click', () => {
        if(fp.selectedDates.length === 0) {
            setActiveBox(boxStart, textStart);
        } else {
            setInactiveBox(boxStart, textStart);
            setActiveBox(boxEnd, textEnd);
        }
    });

    // Rendre le calendrier responsive si l'utilisateur redimensionne la fenêtre
    window.addEventListener('resize', () => {
        if(window.innerWidth < 768 && fp.config.showMonths !== 1) {
            fp.set('showMonths', 1);
        } else if (window.innerWidth >= 768 && fp.config.showMonths !== 2) {
            fp.set('showMonths', 2);
        }
    });
});