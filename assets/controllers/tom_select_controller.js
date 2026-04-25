import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller {
    static values = {
        createUrl: String
    };

    connect() {
        const config = {};

        if (this.createUrlValue) {
            config.create = true;
            config.createOnBlur = true;
            config.persist = true;
            config.onOptionAdd = (value, data) => {
                const existingOptions = this.element.options;
                for (let i = 0; i < existingOptions.length; i++) {
                    if (existingOptions[i].value === value) {
                        return;
                    }
                }

                fetch(this.createUrlValue, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ nom: value }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.id) {
                            const option = document.createElement('option');
                            option.value = data.id;
                            option.textContent = data.nom;
                            this.element.appendChild(option);
                            this.tomSelect.addOption({ value: data.id, text: data.nom });
                            this.tomSelect.removeItem(value);
                            this.tomSelect.addItem(data.id);
                        }
                    })
                    .catch(error => console.error('Error creating equipment:', error));
            };
        }

        this.tomSelect = new TomSelect(this.element, config);
    }

    disconnect() {
        if (this.tomSelect) {
            this.tomSelect.destroy();
        }
    }
}
