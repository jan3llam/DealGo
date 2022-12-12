$(function () {
    ;('use strict')

    var dtTable = $('.offices-list-table'),
        newSidebar = $('.new-office-modal'),
        viewSidebar = $('.view-office-modal'),
        newForm = $('.add-new-office'),
        statusObj = {
            1: {title: LANG.Active, class: 'badge-light-success status-switcher'},
            0: {title: LANG.Inactive, class: 'badge-light-secondary status-switcher'}
        }


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.ajax({
                    url: assetPath + 'api/admin/offices/list',
                    data: {
                        length: data.length,
                        lang: $('html').attr('lang'),
                        start: data.start,
                        draw: data.draw,
                        search: data.search.value,
                        status: $('#status_filter').val(),
                        direction: data.order[0].dir,
                        order: data.columns[data.order[0].column].data.replace(/\./g, "__"),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    success: function (res) {
                        if (parseInt(res.code) === 1) {
                            callback({
                                draw: res.data.meta.draw,
                                recordsTotal: res.data.meta.total,
                                recordsFiltered: res.data.meta.count,
                                data: res.data.data
                            });
                        } else {
                            toastr['error'](res.message);
                        }
                    },
                    error: function (res) {
                        if (parseInt(res.status) === 403) {
                            toastr['error'](LANG[res.status]);
                        } else {
                            toastr['error'](res.statusText)
                        }
                    }
                });
            },
            processing: true,
            serverSide: true,
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
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
                    targets: 8,
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
                    targets: 3,
                    render: function (data, type, full, meta) {
                        return data ? data : '-';
                    }
                },

                {
                    targets: 4,
                    render: function (data, type, full, meta) {
                        return data ? data.name + data.state.name : '-';
                    }
                },
                {
                    // Actions
                    targets: -1,
                    title: LANG.Actions,
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
                            LANG.View + '</a>' +
                            '<a href="javascript:;" class="dropdown-item item-update" data-id="' + full['id'] + '">' +
                            feather.icons['edit'].toSvg({class: 'font-small-4 me-50'}) +
                            LANG.Edit + '</a>' +
                            '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' +
                            feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) +
                            LANG.Delete + '</a></div>' +
                            '</div>' +
                            '</div>'
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
                    text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + LANG.Export,
                    buttons: [
                        {
                            extend: 'print',
                            text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + LANG.Print,
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
                            text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + LANG.Copy,
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
                    text: LANG.Status,
                    buttons: [
                        {
                            text: LANG.Active,
                            attr: {
                                "data-status": 1
                            },
                            className: 'status-item dropdown-item',
                        },
                        {
                            text: LANG.Inactive,
                            attr: {
                                "data-status": 0
                            },
                            className: 'status-item dropdown-item',
                        },
                        {
                            text: LANG.Trashed,
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
                    text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + LANG.Delete,
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                    }
                },
                {
                    text: LANG.AddNew,
                    className: 'add-office btn btn-primary',
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
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/' + $('html').attr('lang') + '.json'
            },
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
                'name': {
                    required: function (element) {
                        return parseInt($('#type').val()) === 1;
                    }
                },
                'commercial': {
                    required: function (element) {
                        return parseInt($('#type').val()) === 1;
                    }
                },
                'license': {
                    required: function (element) {
                        return parseInt($('#type').val()) === 1 && parseInt($("#form_status").val()) === 1;
                    }
                },
                'company': {
                    required: function (element) {
                        return parseInt($('#type').val()) === 1 && parseInt($("#form_status").val()) === 1;
                    }
                },
                'password': {
                    equalTo: "#confirm_password",
                    required: function (element) {
                        return parseInt($("#form_status").val()) === 1;
                    },
                    minlength: 6
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
                            if (parseInt($("#form_status").val()) === 1) {
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
                'province': {
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
                            if (parseInt($("#form_status").val()) === 1) {
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

        $('#country,#city,#province').select2({
            dropdownParent: newSidebar
        });

        newForm.on('submit', function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            if (e.isTrigger) {
                return;
            }
            let data = new FormData();
            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';

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
                data.append('province', $('#province').find("option:selected").text());
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/offices/' + type,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    data: data,
                    beforeSend: function () {
                        // setting a timeout
                        $('button[type=submit]').hide();
                        $('#loading-btn').show();
                    },
                    success: function (response) {
                        $('button[type=submit]').show();
                        $('#loading-btn').hide();
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                            newForm[0].reset();
                            newSidebar.modal('hide')
                        } else {
                            toastr['error'](response.message);
                        }
                    },
                    error: function (response) {
                        if (parseInt(response.status) === 403) {
                            toastr['error'](LANG[response.status]);
                        } else {
                            toastr['error'](response.statusText)
                        }
                    }
                })
            }
        })
    }

    $(document).on('click', '.items-delete', function () {
        var ids = dtTable.api().columns().checkboxes.selected()[1];
        if (ids.length) {
            Swal.fire({
                title: LANG.AreYouSure,
                text: LANG.DeleteMsg,
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: LANG.Cancel,
                confirmButtonText: $.validator.format(LANG.ConfirmBulkDelete, [ids.length]),
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE',
                        url: assetPath + 'api/admin/offices/bulk',
                        data: {ids: ids},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                        },
                        success: function (response) {
                            if (parseInt(response.code) === 1) {
                                dtTable.DataTable().ajax.reload();
                                toastr['success'](response.message);
                            } else {
                                toastr['error'](response.message);
                            }
                        },
                        error: function (response) {
                            if (parseInt(response.status) === 403) {
                                toastr['error'](LANG[response.status]);
                            } else {
                                toastr['error'](response.statusText)
                            }
                        }
                    })
                }
            })
        } else {
            Swal.fire({
                title: LANG.Error,
                text: LANG.ChooseErrorMsg,
                icon: 'error',
                confirmButtonText: LANG.Ok,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            })
        }
    });

    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        Swal.fire({
            title: LANG.AreYouSure,
            text: LANG.DeleteMsg,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: LANG.Cancel,
            confirmButtonText: LANG.ConfirmSingleDelete,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: assetPath + 'api/admin/offices/' + element.data('id'),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    },
                    error: function (response) {
                        if (parseInt(response.status) === 403) {
                            toastr['error'](LANG[response.status]);
                        } else {
                            toastr['error'](response.statusText)
                        }
                    }
                })
            }
        })
    })

    $(document).on('click', '.status-switcher', function () {
        let element = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change status for this item?",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: LANG.Cancel,
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
                    url: assetPath + 'api/admin/offices/status/' + element.data('id'),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    },
                    error: function (response) {
                        if (parseInt(response.status) === 403) {
                            toastr['error'](LANG[response.status]);
                        } else {
                            toastr['error'](response.statusText)
                        }
                    }
                })
            }
        })
    });

    $(document).on('click', '.item-view', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data().user;
        viewSidebar.modal('show');
        $('#view-type').html(data.type);
        if (data.type == 1) {
            $('#view-company-container').show();
        }
        $('#view-legal').html('<a target="_blank" href="' + assetPath + 'images/' + data.legal_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-license').html('<a target="_blank" href="' + assetPath + 'images/' + data.license_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-company').html('<a target="_blank" href="' + assetPath + 'images/' + data.company_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-name').html(data.full_name);
        $('#view-contact').html(data.contact_name);
        $('#view-commercial').html(data.commercial_number);
        $('#view-email').html(data.email);
        $('#view-phone').html(data.phone);
        $('#view-country').html(data.city.country.name).trigger('change.select2');
        $('#view-city').html(data.city.name);
        $('#view-province').html(data.province);
        $('#view-address-1').html(data.address_1);
        $('#view-address-2').html(data.address_2);
        $('#view-zip').html(data.zip_code);

    })

    $(document).on('click', '.item-update', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#modals-slide-in').modal('show')
        $('#form_status').val(2);
        $('#object_id').val(data.id);
        data = data.user;
        $('#edit-legal').html('<a target="_blank" href="' + assetPath + 'images/' + data.legal_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#edit-license').html('<a target="_blank" href="' + assetPath + 'images/' + data.license_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#edit-company').html('<a target="_blank" href="' + assetPath + 'images/' + data.company_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#name').val(data.full_name);
        $('#contact').val(data.contact_name);
        $('#province').val(data.province);
        $('#commercial').val(data.commercial_number);
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
        $('#email').val(data.email);
        $('#phone').val(data.phone);
        $('#country').val(data.city.country.id).trigger('change.select2');
        $('#city_id').val(data.city.id);
        $('#province_id').val(data.province);
        $('#address_1').val(data.address_1);
        $('#address_2').val(data.address_2);
        $('#zip').val(data.zip_code);
        $('#type').val(data.type).trigger('change');
    });

    $(document).on('click', '.add-office', function () {
        if ($('#form_status').val() != 1) {
            newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                $(this).val('');
            });
            $("#legal").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
            $("#company").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
            $("#license").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
            $('#edit-legal').html('');
            $('#edit-license').html('');
            $('#edit-company').html('');
        }
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        $('#country').val('').trigger('change.select2');
        $('#city').empty();
        $('#city').val('').trigger('change.select2');
        $('#province').empty();
        $('#province').val('').trigger('change.select2');
    });
})
