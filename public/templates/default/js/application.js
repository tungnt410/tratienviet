/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 var $action;
 $(document).ready(function () {
    init();
});

 function init() {
    $('#search-notify').html('');
    catchEventAdd();
    catchEventEdit();
    catchEventRemove();
    catchEventSaveObject();
    catchEventDeleteObject();
    catchEventSignup();
    catchEventModalHidden();
    editor = new MediumEditor('#newspaper .content');
    $('#newspaper .content').mediumInsert({
        editor: editor
    });
    catchEventNewsPaperSubmit();
}

function catchEventNewsPaperSubmit() {
    $('#newspaper').submit(function (e) {
        e.preventDefault();
        var name = $(this).data('name');
        var data = new Object();
        $.each($(this).find('input, select'), function () {
            data[$(this).attr('name')] = $(this).val();
        });
        if ($(this).find('input[name=type]:checked').length === 0) {
            data['type'] = 0;
        } else {
            data['type'] = 1;
        }
        data['content'] = editor.serialize()["element-0"].value;
        console.log(data);
        var url = $(this).attr('action');
        console.log('save');
        $.ajax({
            url: url,
            data: data,
            method: 'POST'
        }).done(function (respone) {
            console.log(respone);
            try {
                var result = JSON.parse(respone);
                if (result.status === 2) {
                    alert(result.message);
                    return;
                }
                if(name == 'category'){
                    window.location.href = "/category";
                }else{
                    window.location.href = "/news/admin";
                }
                
            } catch (e) {

            }
        });
    });
}

function catchEventModalHidden() {
    $('#modal_info').on('hidden.bs.modal', function () {
        $('#modal_info .modal-body input, #modal_info .modal-body textarea').each(function () {
            $(this).val('');
        });
        $('.save_object').prop('disabled', false);
    });
}

function catchEventSignup() {
    $('form.signup').on('submit', function () {
        if ($('form .agree').is(':checked')) {
            console.log('agree');
            return true;
        } else {
            console.log('event sign up');
            $('#modal-notice .modal-body').append('<p>To create an account, you must agree to MMMS2018 Terms of Service!</p>')
            $('#modal-notice').modal('show');
            return false;
        }
    });
}

function catchEventEdit() {
    $('.edit_object').on('click', function () {
        $action = 'update';
        var $row = $(this).parents('tr');
        $('#modal_info .modal-body input, #modal_info .modal-body textarea').each(function () {
            if ($(this).attr('type') !== 'file') {
                $(this).val($row.find('td.' + $(this).attr('name')).text());
            }
        });
        $('#modal_info .modal-title').html('Edit info ' + $row.find('td.title').text());
        $('#modal_info .only-edit').show();
        $('#modal_info').modal('show');
    });
}

function catchEventAdd() {
    $('#add_object').on('click', function () {
        $('#modal_info .only-edit').hide();
        $('#modal_info').modal('show');
        $action = 'create';
    });
}

function catchEventRemove() {
    $('.remove_object').on('click', function () {
        var $row = $(this).parents('tr');
        $('#modal_delete .modal-body').html('Are you sure to remove ' + $row.find('td.title').text() + '?');
        $('#modal_delete').data('id', $row.find('td.id').text());
        $('#modal_delete').modal('show');
    });
}
function catchEventChangeField() {
    $('#modal_info .modal-body input').on('keypress', function () {
        console.log('on change');
        $(this).css('border-color', '');
        $(this).parent().find('.message-required').remove();
        $('.save_object').prop('disabled', false);
    });
}
function catchEventSaveObject() {
    $('.save_object').on('click', function () {
        var $data = new Object();
        var hasFieldEmpty = false;
        $('#modal_info .modal-body input, #modal_info .modal-body textarea').each(function () {
            var value = $(this).val().trim();
            if ($(this).is(':checkbox')) {
                value = +$(this).is(':checked');
            }
            $data[$(this).attr('name')] = value;
            if ($(this).is(':visible') && value.length === 0) {
                $(this).css('border-color', 'red');
                $(this).parent().append('<p class="message-required">*This field is required!</p>');
                $('.save_object').prop('disabled', true);
                hasFieldEmpty = true;
            }
        });
        if (hasFieldEmpty) {
            catchEventChangeField();
            return;
        }
        if ($('#modal_info form').attr('action').length > 0) {
            $url = $('#modal_info form').attr('action');
            $('#modal_info form').attr('action', $url + $action);
            $('#modal_info form').submit();
        }
        $('.save_object').prop('disabled', true);
        $.ajax({
            url: $('#modal_info').data('controller-name') + '/' + $action,
            data: $data,
            method: 'POST'
        }).done(function (data) {
            var result = JSON.parse(data);
            if (result.status === 2) {
                $('#modal_info .error-notifice').html(result.message);
                catchEventChangeField();
                return;
            }
            if ($action === 'create') {
                $row = $('.info-pattern').clone().removeClass('info-pattern');
                $row.attr('id', $('#modal_info').data('controller-name') + '-' + result.id);
                $('#modal_info .modal-body input').each(function () {
                    $row.find('.' + $(this).attr('name')).html($(this).val());
                });
                $row.find('.id').html(result.id);
                $('tbody').append($row);
                reloadOrder();
                catchEventEdit();
                catchEventRemove();
            } else if ($action === 'update') {
                var $row = $('#' + $('#modal_info').data('controller-name') + '-' + $('#modal_info .modal-body input[name=id]').val());
                $('#modal_info .modal-body input').each(function () {
                    $row.find('.' + $(this).attr('name')).html($(this).val());
                });
            }
            $('#modal_info').modal('hide');
        });
    });
}

function reloadOrder() {
    console.log('reload order');
    $.each($('tbody tr'), function (index) {
        $(this).find('.order').html(index + 1);
    });
}

function catchEventDeleteObject() {
    $('.delete-object').on('click', function () {
        var $data = new Object();
        $data['id'] = $('#modal_delete').data('id');
        console.log($data);
        $.ajax({
            url: '/' + $('#modal_info').data('controller-name') + '/destroy',
            data: $data,
            method: 'POST'
        }).done(function (data) {
            $('#' + $('#modal_info').data('controller-name') + '-' + $('#modal_delete').data('id')).remove();
            $('#modal_delete').modal('hide');
            reloadOrder();
        });
    });
}

function catchEventSearch() {
    $('#search').on('click', function () {
        var data = new Object();
        console.log(data);
        $('.search .form-control').each(function () {
            var value = $(this).val().trim();
            if (value.length > 0) {
                data[$(this).attr('name')] = value;
            }
        });
        $('tbody').html('');
        $('#search').prop('disabled', true);
        $.ajax({
            url: $('#modal_info').data('controller-name') + '/search',
            data: data,
            method: 'POST'
        }).done(function (data) {
            var result = JSON.parse(data);
            if (result.length > 0) {
                $.each(result, function (index) {
                    var row = $('.info-pattern').clone().removeClass('info-pattern');
                    $.each(this, function (key, value) {
                        row.find('td.' + key).html(value);
                    });
                    row.find('td.order').html(index + 1);
                    row.attr('id', row.attr('id') + this.id);
                    $('tbody').append(row);
                });
                init();
            } else {
                $('#search-notify').html('<h1>No matching results!</h1>');
            }

            $('#search').prop('disabled', false);
        });
    });
}

function uploadFile(){
    var inp = document.getElementById('paper-file');
    if(inp.files.length == 0){
        alert("\"Vui lòng upload file Abstract\" / \"Please upload Abstract file\"");
        $('#paper-file').focus();
        return false;
    }

    var checkedNumber = $(':checkbox:checked').length;
    if(checkedNumber != 1){
        alert("\"Vui lòng lựa chọn ít nhất một hình thức báo cáo\" / \"Please choose at least one type of presenation\"");
        $('.checkbox').focus();
        return false;
    }
}