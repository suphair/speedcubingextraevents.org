var competitionevent = $('[data-competitionevent]').data('competitionevent');
$('[data-competitionevent-list=' + competitionevent + ']').addClass('select');


function ValueEnter() {
        
    var CutoffCheck;
    var Values = [];
    var Amounts = [];
    var disciptions = [];
    var descriptions_pre = [];
    for (i = 1; i <= Attemption; i++) {
        disciptions[i] = "";
        descriptions_pre[i] = "";
    }

    $("#limit").css('color', 'black');
    $("#limit_pre").css('color', 'black');
    $('#limit_pre').html('<i class="fas fa-check"></i>');
    for (i = 1; i <= Attemption; i++) {
        $('#value' + i).css('color', 'black');
        Values[i] = $('#value' + i).val();
        Amounts[i] = $('#amount' + i).val();
        if (!limits[i]) {
            $('#value' + i).css('color', 'red');
            $('#limit').css('color', 'red');
            $("#limit_pre").css('color', 'red');
            $('#limit_pre').html('<i class="fas fa-exclamation-circle"></i>');
            descriptions_pre[i] = '<i class="fas fa-exclamation-circle"></i>';
            disciptions[i] = ' Not passed Limit';
        }
    }

    if (isCutoff) {
        $("#cutoff").css('color', 'black');
        for (i = 1; i <= Attemption; i++) {
            if (limits[i]) {
                $('#value' + i).css('color', 'black');
            }
        }
        CutoffCheck = false;
        for (i = 1; i < CutoffN; i++) {
            CutoffCheck = cutoffs[i] || CutoffCheck;
        }
        if (!CutoffCheck) {
            for (i = CutoffN; i <= Attemption; i++) {
                $('#value' + i).css('color', 'red');
                //document.getElementById('value'+ i).style.color='red';        
                if (Values[i] !== "") {
                    descriptions_pre[i] = '<i class="fas fa-exclamation-circle"></i>';
                    disciptions[i] = 'Not passed Cutoff';
                }
            }
            $('#cutoff').css('color', 'red');
            $('#cutoff_pre').css('color', 'red');
            $('#cutoff_pre').html('<i class="fas fa-exclamation-circle"></i>');
            $('#cutoff_hr').css('background', 'red');
        } else {
            $('#cutoff').css('color', 'black');
            $('#cutoff_pre').css('color', 'black');
            $('#cutoff_pre').html('<i class="fas fa-check"></i>');
            $('#cutoff_hr').css('background', 'green');
        }

    }
    for (i = 1; i <= Attemption; i++) {
        if ((!isCutoff || CutoffCheck || i < CutoffN) && Values[i] === '') {
            descriptions_pre[i] = '<i class="fas fa-exclamation-circle"></i>';
            disciptions[i] = 'No result';
        }
        if (document.getElementById('amount' + i) !== null) {
            if (Values[i] === '' && Amounts[i] !== '') {
                descriptions_pre[i] = '<i class="fas fa-exclamation-circle"></i>';
                disciptions[i] = 'No time';
            }
            if (Values[i] !== '' && Amounts[i] === '') {
                descriptions_pre[i] = '<i class="fas fa-exclamation-circle"></i>';
                disciptions[i] = 'No amount';
            }
            if (Values[i] !== 'DNS' && Values[i] !== 'DNF' && Amounts[i] === '0') {
                descriptions_pre[i] = '<i class="fas fa-exclamation-circle"></i>';
                disciptions[i] = 'No amount';
            }


        }
    }
    var AttempsWarning = '';

    submitResult = "";
    for (i = 1; i <= Attemption; i++) {
        $('#description' + i).html(disciptions[i]);
        $('#description' + i + '_pre').html(descriptions_pre[i]);
        if (disciptions[i] !== '') {
            submitResult = submitResult + i + ': ' + disciptions[i] + '\n';
            AttempsWarning = AttempsWarning + i + ',';
        }
    }
    $('#AttempsWarning').val(AttempsWarning);
}

function ValueEnterOne(i) {
    
var input_time=$('#value'+i);
    
    input_time.data('value-cutoff-pass','');
    input_time.data('value-limit-pass','');
    
    var value = input_time.val();

    $('[data-form-scoretaker-confirm]').prop("disabled", false);
    var next_value = 'value' + (i + 1);
    var next_amount = 'amount' + (i + 1);
    value = value.replace('(', '');
    value = value.replace(')', '');
    if ((value.length === 1 & 'вВаАdDfF*'.indexOf(value) !== -1) | value === 'DNF') {
        value = 'DNF';
        $('#amount' + i).val(0);
        AmountEnterOne(i);
        if (i < Attemption) {
            if (document.getElementById(next_value) !== null) {
                document.getElementById(next_value).focus();
            }
            if (document.getElementById(next_amount) !== null) {
                document.getElementById(next_amount).focus();
            }
        }
    } else if ((value.length === 1 & 'ыЫsS/'.indexOf(value) !== -1) | value === 'DNS') {
        value = 'DNS';
        $('#amount' + i).val(0);
        AmountEnterOne(i);
        if (i < Attemption) {
            if (document.getElementById(next_value) !== null) {
                document.getElementById(next_value).focus();
            }
            if (document.getElementById(next_amount) !== null) {
                document.getElementById(next_amount).focus();
            }
        }
    } else {
        value = value.replace(/\D+/g, '');
        value = value.replace(/^0+/, '');
        value = value.substring(0, 7);

        var minute = 0;
        var second = 0;
        var milisecond = 0;

        if (value.length === 1) {
            value = '0.0' + value;
        } else if (value.length === 2) {
            value = '0.' + value;
        } else if (value.length === 3) {
            second = Number.parseInt(value.substr(0, 1));
            value = value.substr(0, 1) + '.' + value.substr(1, 2);
        } else if (value.length === 4) {
            second = Number.parseInt(value.substr(0, 2));
            value = value.substr(0, 2) + '.' + value.substr(2, 2);
        } else if (value.length === 5) {
            second = Number.parseInt(value.substr(1, 2));
            minute = Number.parseInt(value.substr(0, 1));
            value = value.substr(0, 1) + ':' + value.substr(1, 2) + '.' + value.substr(3, 2);
        } else if (value.length === 6) {
            second = Number.parseInt(value.substr(2, 2));
            minute = Number.parseInt(value.substr(0, 2));
            milisecond = Number.parseInt(value.substr(4, 2));
            if (milisecond >= 50) {
                second = second + 1;
            }
            if (second === 60) {
                second = 0;
                minute = minute + 1;
            }
            value = ('0' + minute).substr(-2, 2) + ':' + ('0' + second).substr(-2, 2) + '.00';
        } else {
            value = '';
        }
    }


    if (isCutoff) {
        cutoffs[i] = (value !== '' && value !== 'DNF' && value !== 'DNS' && (second + minute * 60) < (cutoff_second + cutoff_minute * 60));
    }
    limits[i] = (value === '' || value === 'DNF' || value === 'DNS' || (second + minute * 60) < (limit_second + limit_minute * 60));

    input_time.val(value);
    if (value === 'DNF' | value === 'DNS') {
        input_time.css('background', 'yellow');
    } else {
        input_time.css('background', 'white');
    }
    ValueEnter();
}

function AmountEnterOne(i) {
    if (document.getElementById('amount' + i) === null) {
        return;
    }

    var amount = $('#amount' + i).val();
    $('[data-form-scoretaker-confirm]').prop("disabled", false);
    amount = amount.replace(/\D+/g, '');
    $('#amount' + i).val(amount);

    if (amount === '0') {
        $('#amount' + i).css('background', 'yellow');
    } else {
        $('#amount' + i).css('background', 'white');
    }
    ValueEnter();
}


function chosenSelectCommandID(n) {
    $('#Registration option').each(function () {
        $(this).removeAttr("selected");
    });
    $("#CommandIDSelect" + n).attr("selected", "selected");
    $(".chosen-select").trigger("chosen:updated.chosen");
    PrepareInputs(false);

    if (n > 0) {
        $('#Type').val('Command');
        var team = $('[ data-attempts-team = ' + n + ']')
        for (var i = 1; i <= Attemption; i++) {
            var attempt = team.find('[data-attempt-number = ' + i + ']');
            $('#amount' + i).val(attempt.data('attempt-amount'));
            $('#value' + i).val(attempt.data('attempt-time'));
            //$('#amount' + i).val(AmountsSave[n + '_' + i]);
            //$('#value' + i).val(ValuesSave[n + '_' + i]);

            ValueEnterOne(i);
            AmountEnterOne(i);
        }
    } else {
        $('#Type').val('');
    }
}


function  chosenSelectCompetitorID() {
    $('#Type').val('Competitors');
    for (var i = 1; i <= Attemption; i++) {
        $('.description' + i).html('');
    }

    $('[data-form-scoretaker-confirm]').prop("disabled", false);

    PrepareInputs(true);

    for (var i = 1; i <= Attemption; i++) {
        ValueEnterOne(i);
        AmountEnterOne(i);
    }
}


function ClickRow(n) {
    $('#Type').val('Command');
    $(".chosen-select").val($(".search-field").val());

    updatedChosen();
    $('.CommandSelect').each(function () {
        $(this).removeAttr("disabled");
    });

    chosenSelectCommandID(n);

    setChosenOptions(1);
    $('#value1').focus();
    $('#amount1').focus();
}

function setChosenOptions(n) {
    $('.chosen-select').
            chosen('destroy').
            chosen({max_selected_options: n});
}

function updatedChosen() {
    $('.chosen-select').
            trigger('chosen:updated.chosen');
}


function PrepareInputs(disabled) {
    $('.amount_input, .value_input')
            .css('background', 'white')
            .prop('disabled', disabled)
            .val('');
    $('[data-form-scoretaker-confirm]').prop('disabled', !disabled);
    $('.description').html('');

}


var reg_add_form = $('[data-registration-add-form]');
reg_add_form.find('[data-wcaid]').keyup(function () {

    var icon = reg_add_form.find('[data-icon]');
    var button = reg_add_form.find('[data-button]');
    var result = reg_add_form.find('[data-result]');
    var wcaid_prev = reg_add_form.find('[data-wcaid-prev]');

    if ($(this).val().indexOf('_') + 1 === 0) {
        if (wcaid_prev.data('wcaid-prev').toUpperCase() !== $(this).val().toUpperCase()) {
            result.html('search...');
            button.hide();
            icon.removeClass();
            icon.addClass('fas fa-search');
            $.get($(this).data('wcaid') + '&wcaid=' + $(this).val(), function (data) {

                var json = $.parseJSON(data);
                var json_status = json['status'];
                var json_message = json['message'];
                var json_wcaid = json['wcaid'];

                if (json_status !== undefined) {
                    switch (json_status) {
                        case 'error':
                            icon.addClass('fas fa-exclamation-triangle');
                            icon.addClass('color_red');
                            break;
                        case 'done':
                            icon.addClass('fas fa-user-check');
                            icon.addClass('color_green');
                            break;
                        case 'find':
                            icon.addClass('fas fa-user-plus');
                            button.show();
                            break;
                    }
                }

                if (json_message !== undefined) {
                    result.html(json_message);
                }

                if (json_wcaid !== undefined) {
                    wcaid_prev.data('wcaid-prev', json_wcaid);
                }

            });
        }
    } else {
        button.hide();
        result.html('');
        icon.removeClass();
        wcaid_prev.data('wcaid-prev', '');

    }
});

reg_add_form.find('[data-wcaid]').each(function () {
    $(this).mask("9999aaaa99");
    $(this).attr("required", "true");
    $(this).attr("placeholder", "WCA ID");
    $(this).attr("autocomplete", "off");
});

$('[data-window-open]').click(function () {
    window.open($(this).data('window-open'), '_blank');
});

$('[data-confirm-message]').submit(function () {
    return confirm($(this).data('confirm-message'));
});

//jQuery/chosen_v1/docsupport/init.js
var config = {
    '.chosen-select': {},
    '.chosen-select-deselect': {allow_single_deselect: true},
    '.chosen-select-no-single': {disable_search_threshold: 10},
    '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
    '.chosen-select-rtl': {rtl: true},
    '.chosen-select-width': {width: '95%'}
}
for (var selector in config) {
    $(selector).chosen(config[selector]);
}

PrepareInputs(false);
$('.chosen-search-input').focus();

var form = $('[data-form-scoretaker]');

form.keydown(function () {
    if ((event.which || event.keyCode) === 13) {
        fn = function (elements, start) {
            for (var i = start; i < elements.length; i++) {
                var element = elements[i]
                if (element.tagName === 'INPUT' || element.tagName === 'BUTTON') {
                    element.focus();
                    break;
                }
            }
            return i;
        }
        var current = event.target || event.srcElement;
        if (current.name === '') {
            return true;
        }
        for (var i = 0; i < this.elements.length; i++) {
            if (this.elements[i] === current) {
                break;
            }
        }
        if (fn(this.elements, i + 1) === this.elements.length)
            fn(this.elements, 0);
    }
});


form.find('button').click(function () {
    if (submitResult !== '') {
        if (confirm('Wrong results!\n' + submitResult + 'Confirm results anyway?')) {
            form.submit();
        } else {
            return false;
        }
    } else {
        form.submit();
    }
});

$('[data-attempt-warning = 1]').addClass('score_taker_warning');
$('[data-team-onsite = 1]').addClass('fas fa-home');

$('[data-attempts-team] a').click(function () {
    ClickRow(
            $(this).parents('[data-attempts-team]').data('attempts-team')
            );
});

