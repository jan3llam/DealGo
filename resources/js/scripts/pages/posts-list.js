$(function () {
    ;('use strict')

    var dtTable = $('.posts-list-table'),
        newSidebar = $('.new-post-modal'),
        viewSidebar = $('.view-post-modal'),
        newForm = $('.add-new-post');


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        var link = assetPath + 'api/admin/posts/list/';
        var classification_id = $('#classification_id').val();
        if (classification_id) {
            link += classification_id;
        }
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.get(link, {
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
                {data: 'name_translation'},
                {data: 'classification.name_translation'},
                {data: 'created_at'},
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
                '<"col-sm-12 col-lg-3 d-flex justify-content-center justify-content-lg-start" l>' +
                '<"col-sm-12 col-lg-9 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
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
                    }
                },
                {
                    text: 'Add new',
                    className: 'add-post btn btn-primary',
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
        var editor = null;

        newForm.validate({
            errorClass: 'error',
            rules: {
                'classification': {
                    required: true
                },
                'created_at': {
                    required: true
                },
                'updated_at': {
                    required: true
                },
                'meta_name': {
                    required: true
                },
                'meta_description': {
                    required: true
                },
                'meta_image': {
                    required: true
                },
            }
        })

        $('[name^="name"],[name^="description"]').each(function () {
            $(this).rules('add', {
                required: true,
            });
        });

        $('#classification').select2({dropdownParent: newSidebar});

        $("#meta_image").fileinput({'showUpload': false, 'previewFileType': 'any'});

        // editor = new Quill('.editor', {
        //     bounds: '.editor',
        //     modules: {
        //         toolbar: [
        //             [
        //                 {
        //                     font: []
        //                 },
        //                 {
        //                     size: []
        //                 }
        //             ],
        //             ['bold', 'italic', 'underline', 'strike'],
        //             [
        //                 {
        //                     color: []
        //                 },
        //                 {
        //                     background: []
        //                 }
        //             ],
        //             [
        //                 {
        //                     script: 'super'
        //                 },
        //                 {
        //                     script: 'sub'
        //                 }
        //             ],
        //             [
        //                 {
        //                     header: '1'
        //                 },
        //                 {
        //                     header: '2'
        //                 },
        //                 'blockquote',
        //                 'code-block'
        //             ],
        //             [
        //                 {
        //                     list: 'ordered'
        //                 },
        //                 {
        //                     list: 'bullet'
        //                 },
        //                 {
        //                     indent: '-1'
        //                 },
        //                 {
        //                     indent: '+1'
        //                 }
        //             ],
        //             [
        //                 'direction',
        //                 {
        //                     align: []
        //                 }
        //             ],
        //             ['link'],
        //             ['clean']
        //         ]
        //     },
        //     theme: 'snow'
        // })

        newForm.on('submit', function (e) {
            e.preventDefault();
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
                // data.append('description', JSON.stringify(editor.getContents()));
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/posts/' + type,
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
            url: assetPath + 'api/admin/posts/' + element.data('id'),
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

    $(document).on('show.bs.tab', 'a[data-bs-toggle="tab"]', function (e) {
        var language = e.target.dataset.language;
        $('.tab-pane.active').removeClass('active').addClass('hidden');
        $('#name-tab-' + language).addClass('active').removeClass('hidden');
        $('#description-tab-' + language).addClass('active').removeClass('hidden');
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
                        url: assetPath + 'api/admin/posts/bulk',
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

    $(document).on('click', '.item-update', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#modals-slide-in').modal('show')
        $('#form_status').val(2);
        $('#classification').val(data.classification_id).trigger('change.select2');
        $('#meta_name').val(data.meta_name);
        $('#meta_description').val(data.meta_description);
        $('#created_at').val(data.created_at);
        $('#updated_at').val(data.updated_at);
        for (const [key, value] of Object.entries(data.name)) {
            $('[name="name[' + key + ']"]').val(data.name[key]);
            $('[name="description[' + key + ']"]').val(data.description[key]);
        }
        $("#meta_image").fileinput('destroy').fileinput({
            initialPreview: [assetPath + 'images/' + data.meta_file],
            showUpload: false,
            initialPreviewAsData: true,
        });
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-post', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        $("#meta_image").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
        // editor.deleteText(0, editor.getLength());
        newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        });
        $('#classification').val($('#classification_id').val()).trigger('change.select2')
    });
})