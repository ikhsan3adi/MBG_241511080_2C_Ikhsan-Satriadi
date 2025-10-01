<script>
    /**
     * Universal input form validator
     * @param {HTMLFormElement} inputElement
     * @param {function(any): (string|false|null)} validator
     * @param {HTMLElement?} feedbackElement
     * @returns boolean
     */
    function validateInputForm(
        inputElement,
        validator,
        feedbackElement = null
    ) {
        const invalidMsg = validator(inputElement.value);

        if (invalidMsg) {
            inputElement.classList.remove("is-valid");
            inputElement.classList.add("is-invalid");

            if (feedbackElement) {
                feedbackElement.textContent = invalidMsg;
            }

            return false;
        }

        inputElement.classList.remove("is-invalid");
        inputElement.classList.add("is-valid");

        if (feedbackElement) {
            feedbackElement.textContent = '';
        }

        return true;
    }
</script>