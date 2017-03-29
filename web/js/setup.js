$(document).ready(function() {
        $("#field_type").select2({ width: '170px' });

        $('#save_inventory').on('click', function () {
            sweetAlert({
                title: "Confirm Save",
                text: "Are you sure you want proceed saving?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Proceed Save",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function () {
                if ($('.fields').length >= 1) {
                    $fields = [];

                    $('.fields').each(function() {
                        $fields.push($(this).val());
                    });
                
                    $.post(
                        '/setup/save',
                        {
                            'fields': $fields,
                            'table' : $('#table_name').val()
                        }
                    ).done(function (data) {
                        if (data.error) {
                            sweetAlert({
                                title: "Save Failed",
                                text: "Table name already exists. Please try another.",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Close",
                                closeOnConfirm: true
                            }, function () {
                                $('#table_name').val('');
                                $('#table_name').focus();
                                return;
                            });
                        }
                        else {
                            window.location.href = "/inventory";
                        }
                    });
                }
                else {
                    sweetAlert({
                        title: "Save Failed",
                        text: "Perhaps you forgot to create fields for this inventory.",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Close",
                        closeOnConfirm: true
                    }, function () {
                        $('#field_name').val('');
                        $('#field_name').focus();
                        return;
                    });
                }
            });
        });

        $('#add_field').on('click', function ()  {
            if ($('#table_name').val()) {
                if ($('#field_name').val() && $('#field_type').val()) {
                    $field_name = $('#field_name').val();
                    $field_name = $field_name.toLowerCase();
                    $field_name = $field_name.replace(/\s/g, "_");

                    if ($('#hidden_'+$field_name).val()) {
                        sweetAlert({
                            title: "Field Name Exists",
                            text: "Field name: " + $('#field_name').val() + " already exists. Please try another one.",
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Close",
                            closeOnConfirm: true
                        }, function () {
                            $('#field_name').val('');
                            $('#field_name').focus();
                            return;
                        });

                        return;
                    } else {
                        $('#table_name_display').text($('#table_name').val());
                        $('#save_inventory').show();

                        $class = '';
                        if ($('#field_type').val() == 'text') {
                            $class = 'mdl-data-table__cell--non-numeric';
                        }

                        $field = $field_name +':'+ $('#field_type').val();

                        $('.tr-head').append('<th class="'+$class+'" id="th_'+ $field_name +'">'+ $('#field_name').val() +'</th>');
                        $('.tr-head').append('<input type="hidden" class="fields" id="hidden_'+ $field_name +'" value="'+ $field +'">');
                        $('.tr-body').append('<td class="'+$class+'" id="td_'+ $field_name +'"><a href="javascript:void(0)" class="remove_field" id="'+ $field_name +'">Remove</a></td>');

                        $('#field_name').val('');
                        $('#field_type').select2('val', '');

                        $('#field_name').focus();
        
                        $('.remove_field').on('click', function () {
                            $id = $(this).attr('id');
                            $('#th_' + $id).remove();
                            $('#td_' + $id).remove();
                            $('#hidden_' + $id).remove();

                            if ($('.fields').length < 1) {
                                $('#save_inventory').hide();
                            }
                        });
                    }

                }
                else {
                    sweetAlert({
                        title: "Field Required",
                        text: "Some required fields are missing.",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Close",
                        closeOnConfirm: true
                    }, function () {
                        if (!$('#field_name').val()) {
                            $('#field_name').focus();
                            return;
                        }

                        if (!$('#field_type').val()) {
                            $('#field_type').focus();
                            return;
                        }
                    });
                }
            }
            else {
                sweetAlert({
                    title: "Field Required",
                    text: "Inventory Name is a required field",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Close",
                    closeOnConfirm: true
                }, function () {
                    $('#table_name').focus();
                });
            }
        });
});
