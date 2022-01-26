Dropzone.autoDiscover = false;

$(function () {
    ;('use strict')

    var dtTable = $('.admins-list-table'),
        newSidebar = $('.new-admin-modal'),
        newForm = $('.add-new-admin'),
        statusObj = {
            1: {title: 'Active', class: 'badge-light-success status-switcher'},
            0: {title: 'Inactive', class: 'badge-light-secondary status-switcher'}
        };

    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.DataTable({
            ajax: assetPath + 'api/admin/admins/list',
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
                {data: 'dealgo_id'},
                {data: 'name'},
                {data: 'phone'},
                {data: 'email'},
                {data: 'role'},
                {data: 'status'},
                {data: ''}
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: 'control',
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return ''
                    }
                },
                {
                    targets: 6,
                    render: function (data, type, full, meta) {
                        return data ? data.name : '-';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        var $status = full['status']
                        return (
                            '<span class="badge rounded-pill btn ' +
                            statusObj[$status].class +
                            '" text-capitalized data-id="' + full['id'] + '">' +
                            statusObj[$status].title +
                            '</span>'
                        )
                    }
                },
                {
                    // Actions
                    targets: -1,
                    title: 'Actions',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="btn-group">' +
                            '<a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                            feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) +
                            '</a>' +
                            '<div class="dropdown-menu dropdown-menu-end">' +
                            '<a href="javascript:;" class="dropdown-item item-update" data-id="' + full['id'] + '">' +
                            feather.icons['edit'].toSvg({class: 'font-small-4 me-50'}) +
                            'Edit</a>' +
                            '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' +
                            feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) +
                            'Delete</a></div>' +
                            '</div>' +
                            '</div>'
                        )
                    }
                }
            ],
            order: [[1, 'desc']],
            dom:
                '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
                '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
                '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
                '>t' +
                '<"d-flex justify-content-between mx-2 row mb-1"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
            language: {
                sLengthMenu: 'Showing _MENU_',
                search: 'Search',
                searchPlaceholder: 'Search..'
            },
            createdRow: function (row, data, index) {
                if (data.deleted_at) {
                    $(row).addClass('table-secondary');
                }
            },
            // Buttons with Dropdown
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
                    buttons: [
                        {
                            extend: 'print',
                            text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Print',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'csv',
                            text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'excel',
                            text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'pdf',
                            text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'copy',
                            text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        }
                    ],
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                        $(node).parent().removeClass('btn-group')
                        setTimeout(function () {
                            $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                        }, 50)
                    }
                },
                {
                    text: 'Add new',
                    className: 'add-admin btn btn-primary',
                    attr: {
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modals-slide-in'
                    },
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                    }
                }
            ],
            // For responsive popup
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data()
                            return 'Details of ' + data['name']
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.columnIndex !== 6 // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                col.rowIdx +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<td>' +
                                col.title +
                                ':' +
                                '</td> ' +
                                '<td>' +
                                col.data +
                                '</td>' +
                                '</tr>'
                                : ''
                        }).join('')
                        return data ? $('<table class="table"/>').append('<tbody>' + data + '</tbody>') : false
                    }
                }
            },
        })
    }

    if (newForm.length) {
        let data = new FormData();

        var phone = document.getElementById('phone');

        window.intlTelInput(phone, {
            customContainer: "w-100",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/utils.min.js"
        });

        newForm.validate({
            errorClass: 'error',
            rules: {
                'name': {
                    required: true
                },
                'dealgo_id': {
                    required: true
                },
                'email': {
                    required: true
                },
                'phone': {
                    required: true
                },
                'password': {
                    required: true
                },
                'city': {
                    required: true
                },
                'address': {
                    required: true
                },
            }
        })

        var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';

        $('#files').dropzone({
            url: assetPath + 'api/admin/owners/' + type,
            autoProcessQueue: false,
            addRemoveLinks: true,
            autoQueue: false,
            init: function () {
                this.on("addedfile", function (file) {
                    data.append("files", file);
                });
                this.on("removedfile", function () {
                    data.delete('files');
                });
            }
        });

        $('#country,#city').select2();

        newForm.on('submit', function (e) {
            e.preventDefault();
            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';
            // var data = new FormData();
            //
            // for (var i = 0; i < dataFiles.serializeArray().length; i++) {
            //     data.append(dataFiles[i].name, dataFiles[i].value);
            // }

            if (isValid) {
                if (type === 'update') {
                    data.append('object_id', $('#object_id').val());
                }
                newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                    data.append($(this).attr('name'), $(this).val());
                });
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/owners/' + type,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                })
                newForm[0].reset();
                newSidebar.modal('hide')
            }
        })
    }


    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE',
            url: assetPath + 'api/admin/admins/' + element.data('id'),
            dataType: 'json',
            success: function (response) {
                if (parseInt(response.code) === 1) {
                    dtTable.DataTable().ajax.reload();
                    toastr['success'](response.message);
                } else {
                    toastr['error'](response.message);
                }
            }
        })
    });

    $(document).on('click', '.status-switcher', function () {
        let element = $(this);
        $.ajax({
            type: 'PUT',
            url: assetPath + 'api/admin/admins/status/' + element.data('id'),
            dataType: 'json',
            success: function (response) {
                if (parseInt(response.code) === 1) {
                    dtTable.DataTable().ajax.reload();
                    toastr['success'](response.message);
                } else {
                    toastr['error'](response.message);
                }
            }
        })
    });

    $(document).on('click', '.item-update', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#modals-slide-in').modal('show')
        $('#form_status').val(2);
        $('#name').val(data.contact_name);
        $('#dealgo_id').val(data.dealgo_id);
        $('#email').val(data.email);
        $('#city_id').val(data.city.id);
        $('#country').val(data.city.country.id);
        $('#country').trigger('change.select2');
        $('#address').val(data.address);
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-admin', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        })
    });
})
