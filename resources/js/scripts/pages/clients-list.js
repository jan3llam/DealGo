$(function () {
    ;('use strict')

    var dtTable = $('.clients-list-table'), newSidebar = $('.new-client-modal'), viewSidebar = $('.view-client-modal'),
        newForm = $('.add-new-client');


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        var link = assetPath + 'api/admin/clients/list';

        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.ajax({
                    url: link, data: {
                        length: data.length,
                        lang: $('html').attr('lang'),
                        start: data.start,
                        draw: data.draw,
                        search: data.search.value,
                        status: $('#status_filter').val(),
                        direction: data.order[0].dir,
                        order: data.columns[data.order[0].column].data.replace(/\./g, "__"),
                    }, headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    }, success: function (res) {
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
                    }, error: function (response) {
                        if (parseInt(response.status) === 403) {
                            toastr['error'](LANG[response.status]);
                        } else {
                            toastr['error'](response.statusText)
                        }
                    }
                });
            },
            processing: true,
            serverSide: true,
            columns: [// columns according to JSON
                {data: ''}, {data: 'id'}, {data: 'id'}, {data: 'file', orderable: false}, {
                    data: 'url',
                    orderable: false
                }, {data: ''}],
            columnDefs: [{
                // For Responsive
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0,
                render: function (data, type, full, meta) {
                    return ''
                }
            }, {
                // For Checkboxes
                targets: 1, orderable: false, responsivePriority: 3, render: function (data, type, full, meta) {
                    return ('<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="' + data + '" id="checkbox-' + data + '" /><label class="form-check-label" for="checkbox-' + data + '"></label></div>');
                }, checkboxes: {
                    selectRow: true,
                    selectAllRender: '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
                }
            }, {
                targets: 3, render: function (data, type, full, meta) {
                    return `<a data-fancybox="single" href="${assetPath + 'images/' + data}"><img height="30" src="${assetPath + 'images/' + data}"/></a>`;
                }
            }, {
                targets: 4, render: function (data, type, full, meta) {
                    return `<a href="${data}">` + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + `</a>`;
                }
            }, {
                // Actions
                targets: -1, title: LANG.Actions, orderable: false, render: function (data, type, full, meta) {
                    return ('<div class="btn-group">' + '<a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' + feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) + '</a>' + '<div class="dropdown-menu dropdown-menu-end">' + '<a href="javascript:;" class="dropdown-item item-update" data-id="' + full['id'] + '">' + feather.icons['edit'].toSvg({class: 'font-small-4 me-50'}) + LANG.Edit + '</a>' + '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' + feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + LANG.Delete + '</a></div>' + '</div>' + '</div>')
                }
            }],
            order: [[1, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' + '<"col-sm-12 col-lg-3 d-flex justify-content-center justify-content-lg-start" l>' + '<"col-sm-12 col-lg-9 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' + '>t' + '<"d-flex justify-content-between mx-2 row mb-1"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

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
                Fancybox.bind('[data-fancybox="single"]', {
                    groupAttr: false,
                });
            }, // Buttons with Dropdown
            buttons: [{
                extend: 'collection',
                className: 'btn btn-outline-secondary dropdown-toggle me-2',
                text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + LANG.Export,
                buttons: [{
                    extend: 'print',
                    text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + LANG.Print,
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'csv',
                    text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'CSV',
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'excel',
                    text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'pdf',
                    text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'PDF',
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'copy',
                    text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + LANG.Copy,
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }],
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                    $(node).parent().removeClass('btn-group')
                    setTimeout(function () {
                        $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                    }, 50)
                }
            }, {
                extend: 'collection',
                className: 'btn btn-outline-secondary dropdown-toggle me-2',
                text: LANG.Status,
                buttons: [{
                    text: LANG.Active, attr: {
                        "data-status": 1
                    }, className: 'status-item dropdown-item',
                }, {
                    text: LANG.Trashed, attr: {
                        "data-status": 2
                    }, className: 'status-item dropdown-item',
                }],
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                    $(node).parent().removeClass('btn-group')
                    setTimeout(function () {
                        $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                    }, 50)
                }
            }, {
                className: 'items-delete btn btn-danger me-2',
                text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + LANG.Delete,
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                }
            }, {
                text: LANG.AddNew, className: 'add-client btn btn-primary', attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modals-slide-in',
                    'data-bs-backdrop': 'static',
                    'data-bs-keyboard': 'false'
                }, init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                }
            }],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/' + $('html').attr('lang') + '.json'
            },
        })
    }

    if (newForm.length) {

        newForm.validate({
            errorClass: 'error', rules: {
                'url': {
                    required: true
                }, 'file': {
                    required: function (element) {
                        return parseInt($("#form_status").val()) === 1;
                    }
                },
            }
        })

        $("#file").fileinput({'showUpload': false, 'previewFileType': 'any'});

        newForm.on('submit', function (e) {
            e.preventDefault();
            let data = new FormData();

            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';

            if (isValid) {
                if (type === 'update') {
                    data.append('object_id', $('#object_id').val());
                }
                newForm.find('input[type=text],input[type=date],input[type=url],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                    data.append($(this).attr('name'), $(this).val());
                });
                newForm.find('input[type=file]').each(function () {
                    data.append($(this).attr('name'), $(this)[0].files[0]);
                });
                $.ajax({
                    type: 'POST', url: assetPath + 'api/admin/clients/' + type, dataType: 'json', headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    }, processData: false, contentType: false,
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
                            newForm[0].reset();
                            newSidebar.modal('hide')
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }, error: function (response) {
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
                    confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE',
                        url: assetPath + 'api/admin/clients/bulk',
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
                title: LANG.Error, text: LANG.ChooseErrorMsg, icon: 'error', confirmButtonText: LANG.Ok, customClass: {
                    confirmButton: 'btn btn-primary'
                }, buttonsStyling: false
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
                confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: assetPath + 'api/admin/clients/' + element.data('id'),
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

    $(document).on('click', '.item-update', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#modals-slide-in').modal('show')
        $('#form_status').val(2);
        $("#file").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.file], showUpload: false, initialPreviewAsData: true,
        });
        $('#url').val(data.url);
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-client', function () {
        $('#form_status').val(1);
        $('#file_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('input[type=text],input[type=date],input[type=url],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        })
        $("#file").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
    });
})
