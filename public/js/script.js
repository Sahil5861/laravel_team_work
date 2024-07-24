const SweetAlert = function () {
    const _componentSweetAlert = function () {
        const swalWarningElement = document.querySelector('#sweet_warning');
        if (swalWarningElement) {
            swalWarningElement.addEventListener('click', function () {
                swalInit.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this imaginary file!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                });
            });
        }
    };
    return {
        initComponents: function () {
            _componentSweetAlert();
            _componentSelect2();
            _componentMultiselect();
        }
    }
}();