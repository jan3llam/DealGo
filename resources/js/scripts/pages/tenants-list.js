$(function () {
    ;('use strict')

    var dtTable = $('.tenants-list-table'),
        newSidebar = $('.new-tenant-modal'),
        viewSidebar = $('.view-tenant-modal'),
        newForm = $('.add-new-tenant'),
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
                $.get(assetPath + 'api/admin/tenants/list', {
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
                {data: 'user.full_name'},
                {data: 'user.city.name'},
                {data: 'user.contact_name'},
                {data: 'user.phone'},
                {data: 'user.email'},
                {data: 'user.status'},
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
                    targets: 7,
                    render: function (data, type, full, meta) {
                        var $status = data
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
                    targets: 2,
                    render: function (data, type, full, meta) {
                        return data ? data : '-';
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
                    className: 'add-tenant btn btn-primary',
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
            ],
        })
    }

    if (newForm.length) {
        $(document).on('change', '#type', function () {
            var element = $(this);
            if (parseInt(element.val()) === 1) {
                $('#company-container').show();
            } else {
                $('#company-container').hide();
            }
        });

        var phone = document.getElementById('phone');

        window.intlTelInput(phone, {
            customContainer: "w-100",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/utils.min.js"
        });

        newForm.validate({
            errorClass: 'error',
            rules: {
                'type': {
                    required: true
                },
                'full_name': {
                    required: true
                },
                'commercial_number': {
                    required: true
                },
                'contact': {
                    required: true
                },
                'legal': {
                    required: function (element) {
                        return parseInt($("#form_status").val()) === 1;
                    }
                },
                'email': {
                    required: true,
                    email: true,
                    remote: {
                        url: assetPath + 'api/admin/users/check_field',
                        type: "POST",
                        dataType: "json",
                        data: {
                            email: function () {
                                return $("#email").val();
                            }
                        },
                        dataFilter: function (response) {
                            if ($("#form_status").val() === 1) {
                                return parseInt(JSON.parse(response).code) === 1;
                            }
                            return true;
                        }
                    }
                },
                'zip': {
                    required: true,
                },
                'city': {
                    required: true
                },
                'address_1': {
                    required: true
                },
                'phone': {
                    required: true,
                    remote: {
                        url: assetPath + 'api/admin/users/check_field',
                        type: "POST",
                        dataType: "json",
                        data: {
                            phone: function () {
                                return $("#phone").val();
                            }
                        },
                        dataFilter: function (response) {
                            if ($("#form_status").val() === 1) {
                                return parseInt(JSON.parse(response).code) === 1;
                            }
                            return true;
                        }
                    }
                },
            },
            messages: {
                email: {
                    remote: "This email already in use"
                },
                phone: {
                    remote: "This phone already in use"
                }
            }
        });

        $("#legal,#company,#license").fileinput({'showUpload': false, 'previewFileType': 'any'});

        $('#country,#city').select2({
            dropdownParent: newSidebar
        });
        $('#gtype').select2({
            multiple: true,
            placeholder: "-- Select --",
            dropdownParent: newSidebar
        }).val(null).trigger('change.select2')

        newForm.on('submit', function (e) {
            if (e.isTrigger) {
                return;
            }
            e.stopImmediatePropagation();
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
                    url: assetPath + 'api/admin/tenants/' + type,
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
            url: assetPath + 'api/admin/tenants/' + element.data('id'),
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
            url: assetPath + 'api/admin/tenants/status/' + element.data('id'),
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
        $('#object_id').val(data.id);
        if (data.goods_types.length) {
            var goods_types = [];
            data.goods_types.forEach(item => {
                goods_types.push(item.id);
            })
        }
        $('#gtype').val(goods_types).trigger('change.select2');
        data = data.user;
        $('#name').val(data.full_name);
        $('#contact').val(data.contact_name);
        $('#commercial').val(data.commercial_number);
        $('#email').val(data.email);
        $('#phone').val(data.phone);
        $('#city_id').val(data.city.id);
        $("#legal").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.legal_file],
            showUpload: false,
            initialPreviewAsData: true,
        });
        $("#company").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.company_file],
            showUpload: false,
            initialPreviewAsData: true,
        });
        $("#license").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.license_file],
            showUpload: false,
            initialPreviewAsData: true,
        });
        $('#country').val(data.city.country.id).trigger('change.select2');

        $('#address_1').val(data.address_1);
        $('#address_2').val(data.address_2);
        $('#zip').val(data.zip_code);
        $('#type').val(data.type).trigger('change');

    });

    $(document).on('click', '.item-view', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data().user;
        viewSidebar.modal('show');
        $('#view-type').html(data.type);
        if (data.type == 1) {
            $('#view-company-container').show();
        }
        $('#view-legal').html('<a href="' + assetPath + 'images/' + data.legal_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-license').html('<a href="' + assetPath + 'images/' + data.license_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-company').html('<a href="' + assetPath + 'images/' + data.company_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-name').html(data.full_name);
        $('#view-contact').html(data.contact_name);
        $('#view-commercial').html(data.commercial_number);
        $('#view-email').html(data.email);
        $('#view-phone').html(data.phone);
        $('#view-country').html(data.city.country.name).trigger('change.select2');
        $('#view-city').html(data.city.name);
        $('#view-address-1').html(data.address_1);
        $('#view-address-2').html(data.address_2);
        $('#view-zip').html(data.zip_code);

    })

    $(document).on('click', '.add-tenant', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        });
        $('#gtype').val('').trigger('change.select2');
        $("#legal").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
        $("#company").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
        $("#license").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});

    });
})
