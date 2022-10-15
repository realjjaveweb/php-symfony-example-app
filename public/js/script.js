
// a little modern JS showcase ;)
// ideally, this file should be split into more files separating the code for better readability
// and then merged on deploy
// also for a larger project, I would recommend using TypeScript instead (overkill for this little buddy though)

document.addEventListener('DOMContentLoaded', function (e) {
    const content = document.getElementById('content');
    const ajaxContainerClass = 'ajaxContainer';
    const loadingClass = 'loading';

    const header = document.querySelector('#header');
    const headerH1 = header.querySelector('.headerContainer > h1');
    const menuToggler = header.querySelector('.navbar-toggler');
    const menu = header.querySelector('.navbar-collapse');
    menuToggler.addEventListener('click', () => menu.classList.toggle('show'));

    const addAjaxContainer = (navigationLink) => {
        let newContainer = document.createElement('div');
        newContainer.classList.add('container', ajaxContainerClass);
        newContainer.setAttribute('data-navigation', navigationLink);
        newContainer.style.display = 'none';
        return content.appendChild(newContainer);
    };

    const generateDOMtable = (data) => {
        let table = document.createElement('table');
        table.classList.add('table', 'table-stripped');
        if ((typeof data === 'undefined') || (typeof data[0] !== 'object')) {
            return table; // return empty table
        }
        let headingRow = table.appendChild(document.createElement('tr'));
        headingRow.classList.add('thead-dark');
        // snoop labels from first object
        let objectKeys = Object.keys(data[0]);
        objectKeys.forEach(heading => {
            headingRow.appendChild(document.createElement('th')).innerText = heading;
        });

        data.forEach(dataObject => {
            let dataRow = table.appendChild(document.createElement('tr'));
            objectKeys.forEach(key => {
                let cell = document.createElement('td');
                dataRow.appendChild(cell).innerText = dataObject[key];
            });
        });

        return table;
    };

    const loadTableData = (dataUrl, tableContainer, errorMsg = 'Sorry, something happened, try again later.') => {
        if (tableContainer.querySelector(':scope > .loadingSpinner') === null) {
            let loader = document.createElement('span');
            loader.classList.add('spinner-border', 'loadingSpinner');
            tableContainer.prepend(loader);
        }

        tableContainer.classList.add(loadingClass);
        axios.get(dataUrl, {
            responseType: 'json',
        })
            .then(function (response) {
                // HTTP Status 200 does NOT imply success! :-)
                if ((response?.data?.data ?? false) === false) {
                    tableContainer.innerHTML = errorMsg;
                    console.error('Response is missing the data list.');
                } else {
                    tableContainer.innerHTML = '';
                    tableContainer.append(
                        generateDOMtable(response.data.data)
                    );
                }
            })
            .catch(function (error) {
                tableContainer.innerHTML = errorMsg;
                console.error('Could not get the table data from: ' + dataUrl, error);
            }).finally(function () {
                tableContainer.classList.remove(loadingClass);
            });
    };

    // MENU LOGIC
    // all anchors starting with a "#"
    menu.querySelectorAll('a[href^="#"]').forEach(ajaxLink => {
        ajaxLink.addEventListener('click', (e) => {
            e.preventDefault();
            content.querySelectorAll('.' + ajaxContainerClass).forEach(container => container.style.display = 'none');
            headerH1.innerText = ajaxLink.innerText;
            let targetSelector = '.' + ajaxContainerClass + '[data-navigation="' + ajaxLink.hash + '"]';
            let target = content.querySelector(targetSelector);
            if ((target === null) && ((ajaxLink.dataset.getTableData ?? false) !== false)) {
                target = addAjaxContainer(ajaxLink.hash);
            }
            if (target !== null) {
                target.style.display = 'block';
                if ((ajaxLink.dataset.getTableData ?? false) !== false) {
                    loadTableData(ajaxLink.dataset.getTableData, target);
                }
            } else {
                console.warn(
                    'Ajax navigation was requested, but no target was found and no data url provided: ',
                    ajaxLink,
                    targetSelector
                );
            }
        });
    });

    const displayNoneClass = 'd-none';
    const displayFormMsg = (form, selector, responseMessage) => {
        let msgBox = form.querySelector(selector);
        msgBox.innerHTML = responseMessage ?? msgBox?.dataset?.defaultMessage ?? '...';
        msgBox.classList.remove(displayNoneClass);
    };

    const toggleLoading = form => {
        form.querySelectorAll('[type="submit"]').forEach(el => {
            el.toggleAttribute('disabled');
            el.classList.toggle(loadingClass);
        });
    };

    // Axios note:
    // Using axios in modern browsers seems like an overkill
    // for such a simple use case, plus, it's still an "alpha" version
    // using a simple built-in `fetch()` would be sufficient
    // but hey, this is a showcase :-) 
    const postForms = document.querySelectorAll('form.axios-post-data');
    postForms.forEach(currentForm => {
        currentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // possible form pre-validation would be here...

            const formAction = currentForm.getAttribute('action') ?? '';
            if (formAction === "") {
                console.warn('Automatic form processing was requested, but explicit action definition is missing.');
                return;
            }

            // prevent multiple submitions at once
            toggleLoading(currentForm);
            currentForm.querySelectorAll('.formMessage').forEach(el => el.classList.add(displayNoneClass));
            axios.post(formAction, currentForm, {
                headers: {
                    'Content-Type': 'application/json'
                },
                responseType: 'json',
            })
                .then(function (response) {
                    // HTTP Status 200 does NOT imply success! :-)
                    if ((response?.data?.message ?? false) === false) {
                        displayFormMsg(currentForm, '.formError', null);
                    } else {
                        currentForm.reset();
                        displayFormMsg(currentForm, '.formSuccess', response.data.message);
                    }
                })
                .catch(function (error) {
                    displayFormMsg(currentForm, '.formError', error?.response?.data?.message);
                })
                .finally(function () {
                    toggleLoading(currentForm);
                });
        });
    });

});
