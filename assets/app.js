const onConfirm = (form, button, content) => {
    const row = button.parentNode.parentNode;

    const field = row.querySelector('input[name$="[value]"], textarea[name$="[value]"]');
    field.value = content.map(item => item.id).join(',');

    closeUDW();
};

const openUDW = event => {
    event.preventDefault();

    const form = document.querySelector('form[name="location_settings"]');
    const button = event.target.closest('button');
    const input = button.nextElementSibling;
    const config = JSON.parse(button.dataset.udwConfig);
    const selectedLocations = input.value
        .split(',')
        .map(id => parseInt(id))
        .filter(Number.isInteger);

    ReactDOM.render(
        React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm: onConfirm.bind(this, form, button),
            onCancel: () => closeUDW(),
            selectedLocations,
            ...config,
        }),
        document.getElementById('react-udw')
    );
};

const closeUDW = () => ReactDOM.unmountComponentAtNode(document.getElementById('react-udw'));

const removeRow = event => {
    const button = event.target.closest('button');
    const row = button.parentNode.parentNode;

    row.parentNode.removeChild(row);
};

const attachRowEvents = table => {
    Array
        .from(table.querySelectorAll('tbody tr'))
        .forEach(row => {
            // Quick location selection button
            const browser = row.querySelector('button.location-selector-browse');
            if (browser) {
                browser.removeEventListener('click', openUDW);
                browser.addEventListener('click', openUDW);
            }

            // Quick location selection button
            const button = row.querySelector('button.ibexa-settings-form-remove');
            if (button) {
                button.removeEventListener('click', removeRow);
                button.addEventListener('click', removeRow);
            }
        });
};

window.addEventListener('load', () => {
    const addButton = document.getElementById('ibexa-settings-form-add');
    if (addButton) {
        addButton.addEventListener('click', event => {
            event.preventDefault();

            const tableWrapper = addButton.parentElement.previousElementSibling;
            const collectionHolder = tableWrapper.querySelector('tbody');
            const item = document.createElement('tr');

            item.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);

            collectionHolder.appendChild(item);
            collectionHolder.dataset.index++;

            attachRowEvents(tableWrapper);
        });

        attachRowEvents(addButton.parentElement.previousElementSibling);
    }
});
