var prev = "";
var searchtypes = ["firstname", "lastname"];
let TYPE_FIRSTNAMES = 0;
let TYPE_LASTNAMES = 1;
let RESULT_LIMIT = 100;

function key(e, code) {
    return (e.keyCode || e.which) === code
}

function getresultitemhtml(patient) {
    return "<div class=\"result\"><span>" + patient["lastname"] + ", " + patient["firstname"] + "</span><br><span>AGE years old</span></div>";
}

function pushresults(data, type) {
    clearresulthtml();
    var noresults = true;
    let list = $("div.actuallist")[0];
    for (let i = 0; i < data.length; i++) {
        if (i > RESULT_LIMIT) {
            break;
        }
        let p = data[i];
        if (p[searchtypes[type]].substring(0, prev.length).toLowerCase() === prev.toLowerCase()) {
            list.innerHTML += getresultitemhtml(p);
            noresults = false;
        }
    }
    if (noresults) {
        $("div.noresultstext").show()
    }
}

function search(query, type) {
    $("div.starttext").hide();
    if (query.toLowerCase() === prev || !query.toLowerCase().match(/^[A-Za-z]+$/)) {
        $("div.invalidtext").show();
        $("div.actuallist").hide();
        return;
    }

    hideall();
    $("div.loadingthingy").show();

    prev = query;
    var letters = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
    for (var i = 0; i < letters.length; i++) {
        if (query.substring(0, 1) === letters[i]) {
            $.getJSON("index/" + searchtypes[type] + "/" + letters[i] + ".json", function (data) {
                pushresults(data, type);
                $("div.loadingthingy").hide();
                $("div.actuallist").show()
            })
        }
    }
}

function clearresulthtml() {
    $("div.actuallist").html("")
}

function hideall() {
    $("div.loadingthingy").hide();
    $("div.noresultstext").hide();
    $("div.invalidtext").hide();
    $("div.starttext").hide();
    $("div.actuallist").hide();
}

$(document).ready(function () {

    $("input.searchbar").on("keyup", function (e) {
        if (key(e, 13)) {
            var t = TYPE_FIRSTNAMES;
            if ($("input#lastnameradio").is(":checked")) {
                t = TYPE_LASTNAMES;
            }

            search($("input.searchbar").val(), t)
        }
    });
    $('input#firstnameradio').change(function () {
        if ($(this).is(':checked')) {
            $("input#lastnameradio").prop("checked", false)
        }
        prev = prev + prev;
        if (prev.length > 1000) {
            prev = "";
        }
    });
    $('input#lastnameradio').change(function () {
        if ($(this).is(':checked')) {
            $("input#firstnameradio").prop("checked", false)
        }
        prev = prev + prev;
        if (prev.length > 1000) {
            prev = "";
        }
    });
    $("button#clearresults").on("click", function (e) {
        clearresulthtml();
        hideall();
        $("div.starttext").show();
        $("input.searchbar").val("");
    });

    hideall();
    $("div.starttext").show();

});