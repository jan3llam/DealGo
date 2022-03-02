$(function () {
    ;('use strict')

    var dtTable = $('.offers-list-table'),
        newSidebar = $('.new-offer-modal'),
        newForm = $('.add-new-offer'),
        statusObj = {
            1: {title: 'Active', class: 'badge-light-success status-switcher'},
            0: {title: 'Inactive', class: 'badge-light-secondary status-switcher'}
        }


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }

    var btn = $('#offer_id').val() ?
        [
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
                text: 'Add new',
                className: 'add-response btn btn-primary',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modals-slide-in',
                    'data-bs-backdrop': 'static',
                    'data-bs-keyboard': 'false'
                },
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                }
            }
        ] :
        [
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
            }
        ];

    if (dtTable.length) {
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.get(assetPath + 'api/admin/offers_responses/list', {
                    length: data.length,
                    start: data.start,
                    draw: data.draw,
                    request_id: $('#request_id').val(),
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
                {data: 'owner.user'},
                {data: 'total'},
                {data: 'date'},
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
                    targets: 2,
                    render: function (data, type, full, meta) {
                        return data ? data.contact_name : '-';
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, full, meta) {
                        var sum = 0;
                        full['payments'].forEach(item => {
                            sum += item.value;
                        })
                        return sum.toLocaleString(undefined, {minimumFractionDigits: 0});
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
                            '<a href="javascript:;" class="dropdown-item item-view" data-id="' + full['id'] + '">' +
                            feather.icons['eye'].toSvg({class: 'font-small-4 me-50'}) +
                            'View</a>' +
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
            buttons: btn,
        })
    }

    if (newForm.length) {

        $(document).on('change', '#contract', function () {
            var element = $(this);

            if (parseInt(element.val()) === 1) {
                $('#routes_container').hide();
            } else {
                $('#routes_container').show();
            }

        });

        $('#goods_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown(function () {
                    $(this).find('.goods-select2').select2({dropdownParent: newSidebar});
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $('#payments_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown();
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
                                        text: item.name,
                                    };
                                });
                                return {results: data};
                            }
                        }
                    });
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $('#tenant,#contract,#owner').select2({dropdownParent: newSidebar});

        newForm.validate({
            errorClass: 'error',
            rules: {
                'tenant': {
                    required: true
                },
                'date': {
                    required: true
                },
                'down_value': {
                    required: true
                },
                'down_description': {
                    required: true
                },
                'description': {
                    required: true
                },
            }
        })

        $("#files").fileinput({'showUpload': false, 'previewFileType': 'any'});

        newForm.on('submit', function (e) {
            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';
            let data = new FormData();

            e.preventDefault()
            if (isValid) {
                if (type === 'update') {
                    data.append('object_id', $('#object_id').val());
                }
                data.append('offer', $('#offer_id').val());
                newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                    data.append($(this).attr('name'), $(this).val());
                });
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/offers_responses/' + type,
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
            url: assetPath + 'api/admin/offers_responses/' + element.data('id'),
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
            url: assetPath + 'api/admin/offers_responses/status/' + element.data('id'),
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
        $('#name').val(data.full_name);

        $('#contact').val(data.contact_name);
        $('#commercial').val(data.commercial_number);
        $('#email').val(data.email);
        $('#phone').val(data.phone);
        $('#city_id').val(data.city.id);
        $('#country').val(data.city.country.id).trigger('change.select2');
        $('#type').val(data.type);
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-response', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        });
        $('#owner').trigger('change.select2');
        $("#files").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
    });
})
