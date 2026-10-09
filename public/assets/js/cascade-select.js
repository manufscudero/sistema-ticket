document.addEventListener('DOMContentLoaded', function () {
    const selectPiso = document.getElementById('select_piso');
    const selectDependencia = document.getElementById('select_dependencia');

    if (selectPiso && selectDependencia) {
        selectPiso.addEventListener('change', function () {
            const pisoId = this.value;
            const options = selectDependencia.querySelectorAll('option[data-piso]');

            selectDependencia.value = '';

            if (!pisoId) {
                selectDependencia.disabled = true;
                return;
            }

            selectDependencia.disabled = false;

            options.forEach(option => {
                if (option.getAttribute('data-piso') === pisoId) {
                    option.classList.remove('d-none');
                } else {
                    option.classList.add('d-none');
                }
            });
        });
    }
});