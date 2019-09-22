$(document).ready(function () {
        class ModalWindow {
            constructor(content) { // при создании экземпляра создается модальное окно (невидимое)
                var modal =
                    '<div id="modal_form">' +
                    '<span id="modal_close"><img src="images/close.png" width="20px"></span>' +
                    content +
                    '</div>'+
                    '<div id="overlay">overlay</div>';
                var css = '<link type="text/css" rel="stylesheet" href="modalstyle.css">';

                $('body').append(css+modal);
            }

            close() {
                $('#modal_form').remove();
                $('#overlay').remove();
            }
        }
        ////////////////////////////////////////////////////////////////////

        $('.edit-tool').on('click',function(e) {
            var elementId = $(e.target).attr('id');

            var toolData = {tool: 'get_edit_data',
                            id: elementId};

            $.ajax({
                url: 'tools.php',
                data: toolData,
                type: 'POST',
                dataType: 'json',
                success: function (user) {
                    var modalContent =
                        '<h4 style="color: #003066">Редактирование записи</h4><br>' +
                        '<span class="label">Имя клиента:</span><input class="input-data" type="text" value="'+ user.name +'">' +
                        '<span class="label">Номер телефона:</span><input class="input-data" type="text" value="'+ user.number +'"><br>' +
                        '<span class="label">Статус:</span>' +
                            '<select class="input-data">' +
                                '<option value="start">start</option>' +
                                '<option value="payed">payed</option>' +
                                '<option value="used">used</option>' +
                            '</select><br>' +
                        '<span class="label">API-ключ:</span><input class="input-data" type="text" value="'+ user.api +'">' +
                        '<span class="label">Токен:</span><input class="input-data" type="text" value="'+ user.token +'"><br>' +
                        '<span class="label">Google аккаунт:</span><input class="input-data" type="text" value="'+ user.google_acc +'">' +
                        '<span class="label">Google пароль:</span><input class="input-data" type="text" value="'+ user.google_pass +'"><br>' +
                        '<span class="label">AMO логин админа:</span><input class="input-data" type="text" value="'+ user.amo_admin_login +'">' +
                        '<span class="label">AMO API ключ админа:</span><input class="input-data" type="text" value="'+ user.amo_admin_api +'"><br>' +
                        '<span class="label">Комментарий:</span><input class="input-data" type="text" value="'+ user.comment +'"><br>' +
                        '<div width="100%" align="center">' +
                            '<input type="button" class="safe-btn tool-btn" id="'+elementId+'" value="Сохранить">' +
                            '<input type="button" class="tool-btn" id="cancel" value="Отмена">' +
                        '</div>';

                    var modal = new ModalWindow(modalContent);
                    $('#overlay, #modal_close, #cancel').on('click',function() {modal.close();});

                    $('#modal_form select option[value='+user.state+']').attr('selected','selected');

                    $('.safe-btn').on('click',function () {
                        var inputArr = $('.input-data');
                        var valueArr = [];
                        for(var i=0; i<inputArr.length; i++) {
                            valueArr[i] = $(inputArr[i]).val();
                        }
                        valueArr.push($(e.target).attr('id'));

                        var toolData = {
                            tool: 'safe_edit_data',
                            values: valueArr
                        }

                        $.ajax({
                            url: 'tools.php',
                            data: toolData,
                            type: 'POST',
                            dataType: 'json',
                            error: function(e){console.log('server error :(');}
                        });

                        location.reload();
                    });
                },
                error: function(e){console.log('server error :(');}
            });
        });
    ///////////////////////////////////

        $('.delete-tool').on('click',function(e) {
            var elementId = $(e.target).attr('id');
            var modalContent =
                '<h4 style="color: #003066">Удаление записи</h4><br>' +
                '<span style="color: #003066">Вы уверены, что хотите удалить заявку #'+elementId+'?</span><br><br>' +
                '<div width="100%" align="center">' +
                    '<input type="button" class="safe-btn tool-btn" id="'+elementId+'" value="Удалить">' +
                    '<input type="button" class="tool-btn" id="cancel" value="Отмена">' +
                '</div>';

            var modal = new ModalWindow(modalContent);
            $('#overlay, #modal_close, #cancel').on('click',function() {modal.close();});

            $('.safe-btn').on('click',function () {
                var toolData = {
                    tool: 'safe_delete_data',
                    id: $(e.target).attr('id')
                }

                $.ajax({
                    url: 'tools.php',
                    data: toolData,
                    type: 'POST',
                    dataType: 'json',
                    error: function (e) {
                        console.log('server error :(');
                    }
                });

                location.reload();
            });
        });
        /////////////////////////
    $('.wh-on').on('click',function(e) {
        // var elementId = $(e.target).attr('data-subdomain');
        let serverData = {
            subdomain: $(e.target).attr('data-subdomain'),
            wa_api: $(e.target).attr('data-waapi'),
            wa_token: $(e.target).attr('data-watoken'),
            admin_email: $(e.target).attr('data-adminemail'),
            admin_api: $(e.target).attr('data-adminkey'),
        };

        $.ajax({
            url: 'https://example.com/dashboard/whatsapp_widget/make_wh.php',
            data: serverData,
            type: 'POST',
            success: function (data) {
              console.log("можт сейчас вебхуки установились?");
              console.log(data);
              // location.reload();
            },
            error: function (e) {
                console.log('server error');
            }
        });
    });
    /////////////////////////
    $('.qr-send').on('click',function(e) {
        // var elementId = $(e.target).attr('data-subdomain');
        let serverData = {
            wa_api: $(e.target).attr('data-waapi'),
            wa_token: $(e.target).attr('data-watoken'),
            email: $(e.target).parent().find('#qr-email').val()
        };

        $.ajax({
            url: 'https://example.com/dashboard/whatsapp_widget/make_qr.php',
            data: serverData,
            type: 'POST',
            success: function (data) {
                console.log("qr отправился?");
                console.log(data);

            },
            error: function (e) {
                console.log('server error((');
            }
        });
    });
});


