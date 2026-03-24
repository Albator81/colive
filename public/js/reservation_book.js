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
            if (selectedDates.length > 0) {
                textStart.textContent = instance.formatDate(selectedDates[0], "d M Y");
                textStart.classList.add('filled');
                if (startInput) startInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
            } else {
                textStart.textContent = "Ajouter une date";
                textStart.classList.remove('filled');
                if (startInput) startInput.value = "";
            }

            if (selectedDates.length > 1) {
                textEnd.textContent = instance.formatDate(selectedDates[1], "d M Y");
                textEnd.classList.add('filled');
                if (endInput) endInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
            } else {
                textEnd.textContent = "Ajouter une date";
                textEnd.classList.remove('filled');
                if (endInput) endInput.value = "";
            }

            if (selectedDates.length === 0 || selectedDates.length === 2) {
                if(selectedDates.length === 2) {
                    boxStart.classList.remove('active');
                    boxEnd.classList.add('active');
                } else {
                    boxStart.classList.add('active');
                    boxEnd.classList.remove('active');
                }
            } else if (selectedDates.length === 1) {
                boxStart.classList.remove('active');
                boxEnd.classList.add('active');
            }
        }
    });

    btnClear.addEventListener('click', () => {
        fp.clear();
        boxStart.classList.add('active');
        boxEnd.classList.remove('active');
    });


    boxStart.addEventListener('click', () => {
        boxStart.classList.add('active');
        boxEnd.classList.remove('active');
        if(fp.selectedDates.length === 2) fp.clear();
    });

    boxEnd.addEventListener('click', () => {
        if(fp.selectedDates.length === 0) {
            boxStart.classList.add('active');
        } else {
            boxStart.classList.remove('active');
            boxEnd.classList.add('active');
        }
    });

    window.addEventListener('resize', () => {
        if(window.innerWidth < 768 && fp.config.showMonths !== 1) {
            fp.set('showMonths', 1);
        } else if (window.innerWidth >= 768 && fp.config.showMonths !== 2) {
            fp.set('showMonths', 2);
        }
    });
});
