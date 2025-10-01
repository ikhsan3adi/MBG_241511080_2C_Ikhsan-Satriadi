<script>
    /**
     * Function to show alert element
     * @param {HTMLElement} parent
     * @param {string|string[]|object} content
     * @param {'primary'|'secondary'|'success'|'danger'|'warning'|'info'|'light'|'dark'} type
     * @param {boolean} dismissible
     */
    function showAlert(
        parent,
        content,
        type = 'primary',
        dismissible = true
    ) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} ${dismissible ? 'alert-dismissible fade show' : ''}`;
        alert.setAttribute('role', 'alert');

        if (Array.isArray(content)) {
            alert.innerHTML = content.join('<br>');
        } else if (typeof content === 'object') {
            alert.innerHTML = Object.entries(content)
                .map(([key, value]) => `<strong>${key}</strong>: ${value}`).join('<br>');
        } else {
            alert.textContent = content;
        }

        if (dismissible) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn-close';
            button.setAttribute('data-bs-dismiss', 'alert');
            button.setAttribute('aria-label', 'Close');
            alert.appendChild(button);
        }

        parent.appendChild(alert);
    }

    /**
     * Function to clear all alert elements in parent
     * @param {HTMLElement} parent
     */
    function clearAlerts(parent) {
        parent.innerHTML = '';
    }
</script>