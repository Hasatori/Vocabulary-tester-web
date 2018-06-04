/**
 * Soubor obsahující veškeré funkce pro manipulaci s aplikací. 
 * 
 * @author Oldřich Hradil
 * */

/*
 * 
 * @var {BASE} promněnná odpovádající doméně, na které se aplikace nachází.
 */
var BASE = $('base').attr("href");
/**
 * Uprava url pokud obsahuje zadané znaky. Vyskytují se jako součást url po přihlášení přes facebook.
 * @type String
 */
if (window.location.hash == '#_=_') {
    history.replaceState
            ? history.replaceState(null, null, window.location.href.split('#')[0])
            : window.location.hash = '';
}
/**
 * Poté co je dokument načten, tak zjistí na které adrese se nachází. Uloží do
 * proměnné a použije k označení aktuálního umístění na navigační liště. Současně
 * zjistí rozměry nadpisu aktuální pozice a přizpůsobí mu šířku slideru, jež 
 * slouží pro přecházení mezi stránkami.
 * */
$(document).ready(function () {

    var currentPage = window.location.href.toString().replace(/(.*)\/(.*)/, "$2");
    var folder = window.location.href.toString().replace(/.*\/(.*)\/(.*)/, "$1");

    currentPage = currentPage.split('?')[0];

    switch (currentPage) {
        case '':
            if (folder === 'member') {
                currentPage = 'home';
            } else if (folder === 'test') {
                currentPage = 'testing';
            } else {
                currentPage = 'login';
            }
            break;
        case 'forgottenPassword':
            currentPage = 'login';
            break;
        case 'account':
            currentPage = 'settings';
            break;


    }


    var activeElement = document.getElementById(currentPage);
    activeElement.className = activeElement.className + " active";


    var activeWidth = $('.active').width();
    var activePosition = $('.active').position();

    $('.slider').css('width', activeWidth + 2);
    ;
    $('.slider').css('margin-left', activePosition.left);
    ;
    $('.slider').css('background-color', '#333');




});

/**
 * Přizpusobuje pozici slideru po změně šířky okna.
 * */
$(window).resize(function () {

    var activeWidth = $('.active').width();
    var activePosition = $('.active').position();

    $('.slider').css('width', activeWidth + 2);
    ;
    $('.slider').css('margin-left', activePosition.left);
    ;



});
/*******************************  Registrace *******************************/
$('#registrationForm').on('submit', function (event) {

    $('.loaderWrapper').attr('style', 'display:block;');


});

/*###################################################################*/
/*******************************  Přihlášení *******************************/
/**
 * Po odeslání přihlašovacího formuláře zobrazí načítání.
 * 
 *   * @param {string} event 
 *  
 * */
$('#loginForm').on('submit', function (event) {

    $('.loaderWrapper').attr('style', 'display:block;');


});
/**
 * Pouze spouští načítací animaci po kliknutí na odkaz přihlášení přes 
 * Facebook a také zjišťuje zda je zaškrtnuto pole zůststat přihlášení, 
 * pokud ano upraví původní odkaz a přejde na něj.
 * @param {string} url  
 * */
function fbLogin(url) {
    var rememberMe = document.getElementById('rememberMe').checked;
    console.log(rememberMe);
    $('.loaderWrapper').attr('style', 'display:block;');
    var url = url.replace(/rememberMe%3D.*&/g, 'rememberMe%3D' + rememberMe + '&');

    document.location.href = url;
}

/*###################################################################*/




/*###################################################################*/

/******************************* Zapomenuté heslo *******************************/
/**
 * Po odeslání formuláře zapomenutého hesla zobrazí načítání.
 * 
 * @param {string} event 
 *  
 * */
$('#forgottenPasswordForm').on('submit', function (event) {
    $('.loaderWrapper').attr('style', 'display:block;');

}

);


/*###################################################################*/
/******************************* Languages *******************************/
/**
 * Zmení jazyk aplikace. Odešle AJAXOVÝ požadavek a znovu načte příslušnou 
 * stránku.
 * @param {string} language Jazyk do nějž se má aplikace přepnout. 
 * */
function changeLanguage(language) {
    var language = language;



    $.post(BASE, {

        'language': language


    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {
        document.location.reload();
    });
}
/*###################################################################*/
/**************** ANIMACE MENU ******************/
/**
 * Funkce zabranující přerušení animace menu tlačítka. Zajištěno je to 
 * díky dočasnému vypnutí tlačítka po jeho aktivaci. 
 * */
$(document).ready(function () {

    $('#nav-icon1').click(function () {

        $(this).toggleClass('open');
        setTimeout(function () {
            document.getElementById("nav-icon1").disabled = true;
        }, 1);
        setTimeout(function () {
            document.getElementById("nav-icon1").disabled = false;
        }, 500);

    });
});

/****************************************************************/
/**************** SLOVNÍKY ******************/
/**
 * Otevírá formulář pro editaci slovíčka a naplňuje jej potřebnými daty.
 * @param {type} firstLanguage První jazyk slovníku
 * @param {type} secondLanguage Druhý jazyk slovníku
 * @param {type} dictionaryId Identifikační číslo slovníku
 * @param {type} firstValue První hodnota slovíčka
 * @param {type} secondValue Druhá hodnota slovíčka
 * 
 */
function editVocabulary(firstLanguage, secondLanguage, dictionaryId, firstValue, secondValue) {
    $('#formModal').modal();
    $('#formModalHeading > *').css('display', 'none');
    $('#formModal .modal-body > *').css('display', 'none');
    $('#editVocHeading').css('display', 'block');
    $('#vocabularyForm').css('display', 'block');
    $('#vocabularyForm input').val('');
    $('#typeV').val('editVoc');
    $('#firstLanguageV').val(firstLanguage);
    $('#secondLanguageV').val(secondLanguage);
    $('#dictionaryIdV').val(dictionaryId);
    $('#firstValue').val(firstValue);
    $('#secondValue').val(secondValue);
    $('#firstValueVS').val(firstValue);
    $('#secondValueVS').val(secondValue);
}

/**
 * Otevírá dialogové okno s dotazem pro vymazání slovníku. Při kladné odpovědi odeílá 
 * požadavek na vymazání 
 * @param {integer} dictionaryId Identifikační číslo slovníku
 * @param {string} dicInfo Dodatčná informace připojená k dialogu
 * */
function deleteDictionary(dictionaryId, dicInfo) {


    $('#yesNoDialog').modal();
    $('#delDicHeading').css('display', "block");
    $('#delDicMessage').css('display', 'block');
    $('#dialogMessageExtra').css('display', 'block');
    $('#dialogMessageExtra').text(dicInfo);
    document.getElementById('dialogYes').onclick = function () {
        var currentPage = document.location.href;
        $.post(currentPage, {

            'dictionaryId': dictionaryId,
            'type': 'deleteD'


        }, function (data, textStatus, jqXHR) {

        }
        ).done(function (data) {

            document.location.reload();
        });
    };

}
/**
 * Otevírá dialogové okno s dotazem pro vymazání slovíčka. Při kladné odpovědi odeílá 
 * požadavek na vymazání 
 * @param {integer} dictionaryId Identifikační číslo slovníku jehož součástí je slovíčko 
 * @param {string} firstValue První hodnota slovíčka.
 * @param {string} secondValue Druhá hodnota slovíčka.
 * */
function deleteVocabulary(dictionaryId, firstValue, secondValue) {
    $('#yesNoDialog').modal();
    $('#delVocHeading').css('display', "block");
    $('#delVocMessage').css('display', 'block');
    $('#dialogMessageExtra').css('display', 'block');
    $('#dialogMessageExtra').text(firstValue + '->' + secondValue);
    document.getElementById('dialogYes').onclick = function () {
        var currentPage = document.location.href;
 
        $.post(currentPage, {

            'dictionaryId': dictionaryId,
            'firstValue': firstValue,
            'secondValue': secondValue,
            'type': 'deleteV'



        }, function (data, textStatus, jqXHR) {

        }
        ).done(function (data) {

           document.location.reload(); 
 
    }); 
        
    };
}
/**
 *  Otevirá formulář pro přidání slovíčka. 
 */
function addDictionary() {
    $('#formModal').modal();
    $('#formModalHeading > *').css('display', 'none');
    $('#formModal .modal-body > *').css('display', 'none');
    $('#addDicHeading').css('display', 'block');
    $('#dictionaryForm').css('display', 'block');
    $('#dictionaryForm input').val('');
    $('#type').val('addDic');
}
/**
 * Otevírá formulář pro editaci slovníku a naplňuje jej potřebnými daty.
 * @param {type} dicName Název slovníku
 * @param {type} dicFirstLang První jazyk slovníku
 * @param {type} dicSecondLang Druhý jazyk slovníku
 * @param {type} dicId Identifikační číslo slovníku
 */
function editDictionary(dicName, dicFirstLang, dicSecondLang, dicId) {
    $('#formModal').modal();
    $('#formModalHeading > *').css('display', 'none');
    $('#formModal .modal-body > *').css('display', 'none');
    $('#editDicHeading').css('display', 'block');
    $('#dictionaryForm').css('display', 'block');
    $('#dictionaryName').val(dicName);
    $('#firstLanguage').val(dicFirstLang);
    $('#secondLanguage').val(dicSecondLang);
    $('#dictionaryId').val(dicId);
    $('#type').val('editDic');
}

/*###################################################################*/
/******************************* ZKOUŠENÍ *******************************/

/**
 * Přidá zvolený slovník do tabulky pro vytvoření relace zkoušení. Vytvoří jeho klon a změní id.
 * Současné také odesílá požadavek na úpravu session.
 * @param {type} elementId Identifikační číslo řádku tabulky přidávaného slovníku.
 * @param {type} dicId Identifikační číslo slovníku v databázi.
 */

function addForPractice(elementId, dicId){

    var supplementaryTable = $('.supplementaryTable tbody');
    var count = supplementaryTable.children().length;
    if (count < 9) {
        var row = $('#' + elementId).clone();
        if (document.getElementById(dicId) === null) {

            supplementaryTable.prepend(row);

            row.attr('id', dicId);
            var addColumn = row.children('.add');

            row.attr('onclick', 'remove(' + dicId + ')');
            addColumn.children('span').attr('style', 'transform:rotateY(180deg);');
            addColumn.prependTo(row);
        }
    }

    $.post(document.location.href, {

        'dictionaryId': dicId,

        'type': 'addForCreation'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {
        document.location.reload();

    });


}

/**
 * Vymaže slovník ze seznamu pro vytvoření relace zkoušení. Současné také odesílá
 * požadavek na úpravu session.
 * @param {type} id Identifikační číslo slovníku.
 */
function remove(id) {
    $('#' + id).remove();
    var supplementaryTable = $('.supplementaryTable tbody');
    $.post(document.location.href, {

        'dictionaryId': id,
        'type': 'deleteForCreation'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {
        var count = supplementaryTable.children().length + 1;

        var url = new URL(document.location.href);
        var dicOffset = url.searchParams.get("dicOffset");
        var pracOffset = url.searchParams.get("pracOffset");
        var forPracOffset = url.searchParams.get("forPracOffset");

        if (count.toString().match(/\d+1/g) && forPracOffset !== 0) {
            document.location.href = BASE + 'member/practice?dicOffset=' + dicOffset + '&pracOffset=' + pracOffset + "&forPracOffset=" + (forPracOffset - 10);
        } else
        if (forPracOffset !== (count - 10)) {
            document.location.reload();
        }
    });
}


/**
 * Odesílá požadavek na vytvoření nové relace zkoušení.
 * 
 * */
function createPractice() {
    var supplementaryTable = $('.supplementaryTable tbody');

    var practiceName = $('#practiceName').val();
    var showSuccessRate = $('#showSuccessRate').is(':checked');

    var acceptAfter = $('#acceptAfter').val();

    var dictionaries = [];
    supplementaryTable.children('tr').each(function () {
        var dictionaryId = $(this).attr('id');;
        dictionaries.push(dictionaryId);
    });
    
    dictionaries.length === 0 ? dictionaries = 'empty' : null;
    $.post(BASE + 'member/practice', {

        'practiceName': practiceName,
        'showSuccessRate': showSuccessRate,
        'acceptAfter': acceptAfter,
        'dictionaries': dictionaries,
        'type': 'create'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {
        
        var result = JSON.parse(data);
        if (result[0] === true) {
            document.location.reload();
        } else if (result[0] === false) {
            buildMsg(result[1]);
        }




    });
}

/**
 * Pokračuje do v již vytvoření relaci zkoušeni. Současně ji také může odstartovat pokud ještě nebyla zapnuta.
 * @param {Integer} practiceId Identifikátor relace zkoušení 
 * @param {String} practiceName Název relace    
 * 
 * */
function continuePractice(practiceId, practiceName) {

    $.post(BASE + 'member/practice', {

        'practiceId': practiceId,
        'type': 'continue'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {

        console.log(data);
        var result = JSON.parse(data);

        if (result[0] === true) {
            var practiceId = result[4];
            $('#practiceHeading').text(practiceName);
            $('#myCarousel').carousel('next');
            $('#toTranslate').val(result[1].toString().trim());
            $('.succesRateValue').text(result[2] + '%');

            $('.rightAnswerTooltip').attr('data-original-title', result[3]);
            $('#restartPractice2').attr('onclick', 'restartPractice(' + practiceId + ')');
            $('#practiceDirection').attr('onclick', 'changePDirection(' + practiceId + ')');
            
        } else if (result[0] === false) {


        } else if (result[0] === 'isOver') {
            var practiceContent = JSON.parse(result[1]);
            var practiceId = result[2];
            showResults(practiceContent, practiceName, practiceId);
        }



    });
}
 $('#answerForm').on("keyup", function (event) {
            
                event.preventDefault();
              
                if (event.keyCode === 17) {
               
                    document.getElementById("practiceDirection").click();

                }
               
            }
            );
/**
 * Odesílá formulář pro zkoušení. Reaguje na správnost odpovědi a upravuje formulář.
 */
$('#answerForm').on('submit', function (event) {
    event.preventDefault();
    var wordToKnow = $('#toTranslate').val();
    var translation = $('#translation').val();
    var practiceName = $('#practiceHeading').text();
    $.post(BASE + 'member/practice', {

        'wordToKnow': wordToKnow,
        'translation': translation,
        'type': 'sendAnswer'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {

        var result = JSON.parse(data);
        for(var i=0;i<result.length;i++){
            console.log(result[i]);
        }
        if (result[0] === true) {

            $('#translation').css({'background-color': '#5cb85c', 'color': 'white'});
            $('#continue').css('display', 'block');
            $('#sendAnswer').css('display', 'none');
            $('#sendAnswer').attr('type', 'button');
            $('#tryAgain').css('display', 'none');
            $('#tryAgain').attr('type', 'button');
             $('.succesRateValue').text(result[1] + '%');
            $('#answerForm').on("keyup", function (event) {
            
                event.preventDefault();
              
                if (event.keyCode === 13) {
               
                    document.getElementById("continue").click();

                }
                $('#answerForm').off('keyup');
            }
            );

        } else if (result[0] === false) {
            $('#continue').css('display', 'block');
            $('#sendAnswer').css('display', 'none');
            $('#tryAgain').css('display', 'block');
            $('#translation').css({'background-color': '#d9534f', 'color': 'white'});
        }
        if (result[0] === 'isOver') {
            var practiceContent = JSON.parse(result[1]);
            var practiceId = result[2];
            showResults(practiceContent, practiceName, practiceId);
        } else {
            $('.rightAnswerTooltip').attr('title', result[2]);
            $('.succesRateValue').text(result[1] + '%');
        }
    });

});

/**
 * Otevírá dialogové okno s dotazem pro vymazání relace zkoušení. Při kladné odpovědi odesílá 
 * požadavek na vymazání 
 * @param {Integer} practiceId Identifikátor relace zkoušení 
 * @param {String} practiceName Název relace zkoušení    
 * 
 * */
function deletePractice(practiceId, practiceName) {
    $('#yesNoDialog').modal();
    $('#delPracHeading').css('display', "block");
    $('#delPracMessage').css('display', 'block');
    $('#dialogMessageExtra').css('display', 'block');
    $('#dialogMessageExtra').text(practiceName);
    document.getElementById('dialogYes').onclick = function () {
        $.post(BASE + 'member/practice', {

            'practiceId': practiceId,
            'type': 'delete'

        }, function (data, textStatus, jqXHR) {

        }
        ).done(function (data) {
            var result = JSON.parse(data);
            if (result[0] === true) {
                document.location.reload();
            } else if (result[0] === false) {
                $('.mainErrorWrapper').attr('style', 'display:block;');
                $('#mainError').text(result[1]);
            }

        });
    };

}
//    }

/**
 * Zobrazuje výsledky relace zkoušení. Dává možnost jejího restartování.
 * @param {type} practiceContent Obsah relace zkoušení.
 * @param {type} practiceName Název relace zkoušení
 * @param {type} practiceId Identifikační číslo relace zkoušení
 */
function showResults(practiceContent, practiceName, practiceId) {
    var resultTableBody = $('#resultTableBody');

    $('#resultPracticeName').text(practiceName);
    for (var i = 0; i < practiceContent.length; i++) {
        var right = parseInt(practiceContent[i]['right_answers']);
        var wrong = parseInt(practiceContent[i]['wrong_answers']);

        var successRate = Math.round((right / (right + wrong)) * 10000) / 100;
        var row = $('<tr><td>' + practiceContent[i]['first_value'] + '</td>\n\
\n <td>' + practiceContent[i]['second_value'] + '</td>\\n\
        \n  <td>' + right + '</td>\
         \n<td>' + wrong + '</td>\
\n <td>' + successRate + '%</td>\
</tr>');
        resultTableBody.append(row);
    }
    $('#resultDialog').modal({backdrop: 'static', keyboard: false})
    document.getElementById('restartPractice').onclick = function () {
        restartPractice(practiceId);

    };
}

/**
 * Odesílá požadavek na restarování relace zkoušení.
 * @param {type} practiceId Identifikační číslo relace zkoušení
 */
function restartPractice(practiceId) {
    console.log(practiceId);
    $.post(BASE + 'member/practice', {

        'practiceId': practiceId,
        'type': 'restartPractice'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {

        var result = JSON.parse(data);
        if (result[0] === true) {

            document.location.reload();
        } else if (result[0] === false) {

        }
    });
}

/**
 * Mění směr zkoušení zadané relace zkoušení.
 * @param {type} practiceId Identifikační číslo relace zkoušení
 */
function changePDirection(practiceId) {
    $.post(BASE + 'member/practice', {

        'practiceId': practiceId,
        'type': 'changeDirection'

    }, function (data, textStatus, jqXHR) {

    }
    ).done(function (data) {
        document.location.reload();
    });

}
/**
 * Odesílá požadavek na vyčištění session relace zkoušení.
 */
function clearSessions() {
    $.post(BASE + 'member/practice', {

        'type': 'clear'

    }, function (data, textStatus, jqXHR) {

    });


}
/**
 * Funkce pro zobrazení tooltipu.
 */
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
/****************************************************************/
/*###################################################################*/
/******************************* Dialogová okna *******************************/
/**
 * Upravuje chování dialogových oken. Jejich reakce na otevření zavření apod.
 */
try {
    $('#yesNoDialog').on('hidden.bs.modal', function () {
        $('#yesNoDialogHeading *').css('display', 'none');
        $('#yesNoDialogMessage *').css('display', 'none');
    });
    document.getElementById('dialogNo').onclick = function () {
        $('#yesNoDialog').modal('hide');

    };

    $('#resultDialog').on('hidden.bs.modal', function () {
        $('.loaderWrapper').attr('style', 'display:block;');
        document.location.reload();
    });
    document.getElementById('dialogClose').onclick = function () {
        $('.loaderWrapper').attr('style', 'display:block;');
        $('#resultDialog').modal('hide');

    };
} catch (e) {

}
/****************************************************************/
/**
 * Vytvoří chybové hlášení a přodává jej do stránky.
 * @param {type} msg Zpráva hlášení
 *
 */
function buildMsg(msg) {
    console.log(msg);
    $('.mainMessageWrapper').remove();
    var msg = $(' <h4  class="text-danger text-center mainMessageWrapper">' + msg + '</h4>');
    $('.heading').after(msg);
}
/*###################################################################*/
