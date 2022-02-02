Dropzone.autoDiscover = false;

$(function () {
    ;('use strict')

    var dtTable = $('.vessels-list-table'),
        newSidebar = $('.new-vessel-modal'),
        viewSidebar = $('.view-vessel-modal'),
        newForm = $('.add-new-vessel'),
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
                $.get(assetPath + 'api/admin/vessels/list', {
                    length: data.length,
                    start: data.start,
                    draw: data.draw,
                    search: data.search.value,
                    trashed: $('#trashed').val(),
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
                {data: 'name'},
                {data: 'imo'},
                {data: 'owner.contact_name'},
                {data: 'country.name'},
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
                            '<a href="/admin/crews/' + full['id'] + '" class="dropdown-item" data-id="' + full['id'] + '">' +
                            feather.icons['users'].toSvg({class: 'font-small-4 me-50'}) +
                            'Crew</a>' +
                            '<a href="/admin/maintenances/' + full['id'] + '" class="dropdown-item" data-id="' + full['id'] + '">' +
                            feather.icons['tool'].toSvg({class: 'font-small-4 me-50'}) +
                            'Maintenance</a>' +
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
            initComplete: function () {
                $(document).on('click', '.trashed-item', function () {
                    $('#trashed').val($(this).data('trashed'));
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
                    text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + 'Trashed',
                    buttons: [
                        {
                            text: 'Yes',
                            attr: {
                                "data-trashed": 1
                            },
                            className: 'trashed-item dropdown-item',
                        },
                        {
                            text: 'No',
                            attr: {
                                "data-trashed": 0
                            },
                            className: 'trashed-item dropdown-item',
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
                    className: 'add-vessel btn btn-primary',
                    attr: {
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modals-slide-in'
                    },
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                    }
                }
            ],
        })
    }

    if (newForm.length) {
        let data = new FormData();

        $(document).on('change', '#type', function () {
            var element = $(this);
            if (parseInt(element.val()) === 1) {
                $('#company-container').show();
            } else {
                $('#company-container').hide();
            }
        });

        newForm.validate({
            errorClass: 'error',
            rules: {
                'type': {
                    required: true
                },
                'owner': {
                    required: true
                },
                'country': {
                    required: true
                },
                'name': {
                    required: true
                },
                'imo': {
                    required: true
                },
                'mmsi': {
                    required: true
                },
                'build': {
                    required: true
                },
                'capacity': {
                    required: true
                },
                'image': {
                    required: true
                },
            }
        })
        $('#country,#type,#owner').select2({dropdownParent: newSidebar});

        newForm.on('submit', function (e) {
            e.preventDefault();

            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';
            var data = new FormData();


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
                    url: assetPath + 'api/admin/vessels/' + type,
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
            url: assetPath + 'api/admin/vessels/' + element.data('id'),
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
    })

    $(document).on('click', '.status-switcher', function () {
        let element = $(this);
        $.ajax({
            type: 'PUT',
            url: assetPath + 'api/admin/vessels/status/' + element.data('id'),
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
        $('#name').val(data.name);
        $('#capacity').val(data.capacity);
        $('#owner').val(data.owner_id).trigger('change.select2');
        $('#country').val(data.country_id).trigger('change.select2');
        $('#type').val(data.type_id).trigger('change.select2');
        $("#image").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.image],
            showUpload: false,
            initialPreviewAsData: true,
        });
        $('#imo').val(data.imo);
        $('#mmsi').val(data.mmsi);
        $('#build').val(data.build_year);
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-vessel', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        })
        $("#image").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
        $('#owner').val('').trigger('change.select2');
        $('#country').val('').trigger('change.select2');
        $('#type').val('').trigger('change.select2');
    });
})
