$(function () {
    ;('use strict')

    var dtTable = $('.requests-list-table'),
        newSidebar = $('.new-request-modal'),
        viewSidebar = $('.view-request-modal'),
        newForm = $('.add-new-request'),
        statusObj = {
            1: {title: 'Active', class: 'badge-light-success status-switcher'},
            0: {title: 'Inactive', class: 'badge-light-secondary status-switcher'}
        }


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.get(assetPath + 'api/admin/requests/list', {
                    length: data.length,
                    start: data.start,
                    draw: data.draw,
                    search: data.search.value,
                    status: $('#status_filter').val(),
                    direction: data.order[0].dir,
                    order: data.columns[data.order[0].column].data.replace(/\./g, "__"),
                }, function (res) {
                    callback({
                        draw: res.data.meta.draw,
                        recordsTotal: res.data.meta.total,
                        recordsFiltered: res.data.meta.count,
                        data: res.data.data
                    });
                });
            },
            processing: true,
            serverSide: true,
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
                {data: 'id'},
                {data: 'tenant.user'},
                {data: 'port_from.name'},
                {data: 'port_to.name'},
                {data: 'date_from'},
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
                    // For Checkboxes
                    targets: 1,
                    orderable: false,
                    responsivePriority: 3,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="' + data + '" id="checkbox-' +
                            data +
                            '" /><label class="form-check-label" for="checkbox-' +
                            data +
                            '"></label></div>'
                        );
                    },
                    checkboxes: {
                        selectRow: true,
                        selectAllRender:
                            '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, full, meta) {
                        return data ? data.contact_name : '-';
                    }
                },
                {
                    // Actions
                    targets: -1,
                    title: 'Actions',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return (
                            '<a href="/admin/requests_responses/' + full['id'] + '" class="btn btn-light btn-sm" data-id="' + full['id'] + '">' +
                            feather.icons['thumbs-up'].toSvg({class: 'font-small-4 me-50'}) +
                            'Responses (' + full['responses_count'] + ')</a>' +
                            '<a href="javascript:;" class="ms-2 item-delete" data-id="' + full['id'] + '">' +
                            feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + '</a>'
                        )
                    }
                }
            ],
            order: [[1, 'desc']],
            dom:
                '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
                '<"col-sm-12 col-lg-3 d-flex justify-content-center justify-content-lg-start" l>' +
                '<"col-sm-12 col-lg-9 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
                '>t' +
                '<"d-flex justify-content-between mx-2 row mb-1"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',

            createdRow: function (row, data, index) {
                if (data.deleted_at) {
                    $(row).addClass('table-secondary');
                }
            },
            initComplete: function () {
                $(document).on('click', '.status-item', function () {
                    $('#status_filter').val($(this).data('status'));
                    dtTable.DataTable().ajax.reload();
                });
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
                            text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'CSV',
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
                            text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'PDF',
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
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: 'Status',
                    buttons: [
                        {
                            text: 'Active',
                            attr: {
                                "data-status": 1
                            },
                            className: 'status-item dropdown-item',
                        },
                        {
                            text: 'Inactive',
                            attr: {
                                "data-status": 0
                            },
                            className: 'status-item dropdown-item',
                        },
                        {
                            text: 'Trashed',
                            attr: {
                                "data-status": 2
                            },
                            className: 'status-item dropdown-item',
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
                    className: 'items-delete btn btn-danger me-2',
                    text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + 'Delete',
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                        if (!$('#request_id').val()) {
                            node.remove();
                        }
                    }
                },
                {
                    text: 'Add new',
                    className: 'add-request btn btn-primary',
                    attr: {
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modals-slide-in',
                        'data-bs-backdrop': 'static',
                        'data-bs-keyboard': 'false'
                    },
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                        if (!$('#request_id').val()) {
                            node.remove();
                        }
                    }
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/' + $('html').attr('lang') + '.json'
            },
        })
    }

    if (newForm.length) {

        $('#goods_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown(function () {
                    $(this).find('.goods-select2').select2({dropdownParent: newSidebar});
                    $('[name^="goods"]').each(function () {
                        $(this).rules('add', {
                            required: true,
                        });
                    });
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $('#routes_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown(function () {
                    $(this).find('.routes-select2').select2({
                        dropdownParent: newSidebar,
                        ajax: {
                            url: assetPath + 'api/admin/ports/list',
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    search: params.term,
                                    start: params.page || 0
                                }
                            },
                            processResults: function (data) {
                                data = data.data.data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.name_translation,
                                    };
                                });
                                return {results: data};
                            }
                        }
                    });
                    $('[name^="routes"]').each(function () {
                        $(this).rules('add', {
                            required: true,
                        });
                    });
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $(document).on('change', '#contract', function () {
            var element = $(this);
            if (parseInt(element.val()) === 1 || parseInt(element.val()) === 3) {
                $('#owners_container').show();
            } else {
                $('#owners_container').hide();
            }

            if (parseInt(element.val()) === 1) {
                $('#routes_container').hide();
            } else {
                $('#routes_container').show();
            }

        });


        newForm.validate({
            errorClass: 'error',
            rules: {
                'name': {
                    required: true
                },
                'tenant': {
                    required: true
                },
                'port_from': {
                    required: true
                },
                'port_to': {
                    required: true
                },
                'contract': {
                    required: true
                },
                'date_from': {
                    required: true
                },
                'date_to': {
                    required: true
                },
                'description': {
                    required: true
                },
            }
        })


        $('#port_from,#port_to').select2({
            dropdownParent: newSidebar,
            ajax: {
                url: assetPath + 'api/admin/ports/list',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        start: params.page || 0
                    }
                },
                processResults: function (data) {
                    data = data.data.data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name,
                        };
                    });
                    return {results: data};
                }
            }
        });

        $('#tenant,#contract,#owner').select2({dropdownParent: newSidebar});

        $("#files").fileinput({'showUpload': false, 'previewFileType': 'any'});

        newForm.on('submit', function (e) {
            let data = new FormData();
            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';
            e.preventDefault()
            if (isValid) {
                if (type === 'update') {
                    data.append('object_id', $('#object_id').val());
                }
                newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                    data.append($(this).attr('name'), $(this).val());
                });
                newForm.find('input[type=file]').each(function () {
                    data.append($(this).attr('name'), $(this)[0].files[0]);
                });
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/requests/' + type,
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

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete this item!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: assetPath + 'api/admin/requests/' + element.data('id'),
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
            }
        });
    });

    $(document).on('click', '.items-delete', function () {
        var ids = dtTable.api().columns().checkboxes.selected()[1];
        if (ids.length) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete those (' + ids.length + ') rows!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE',
                        url: assetPath + 'api/admin/requests/bulk',
                        data: {ids: ids},
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
                }
            })
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Choose rows to delete',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            })
        }
    });

    $(document).on('click', '.status-switcher', function () {
        let element = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change status for this item?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'PUT',
                    url: assetPath + 'api/admin/requests/status/' + element.data('id'),
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
            }
        })
    });

    $(document).on('click', '.item-update', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#modals-slide-in').modal('show')
        $('#form_status').val(2);
        $("#files").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.files],
            showUpload: false,
            initialPreviewAsData: true,
        });
        $('#name').val(data.full_name);
        $('#contact').val(data.contact_name);
        $('#commercial').val(data.commercial_number);
        $('#email').val(data.email);
        $('#phone').val(data.phone);
        $('#city_id').val(data.city.id);
        $('#country').val(data.city.country.id).trigger('change.select2');
        $('#address_1').val(data.address_1);
        $('#address_2').val(data.address_2);
        $('#zip').val(data.zip_code);
        $('#type').val(data.type);
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-request', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        });
        $("#files").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
    });
})
