let prev = "";
let searchtypes = ["first", "last"];
const TYPE_FIRSTNAMES = 0;
const TYPE_LASTNAMES = 1;
const RESULT_LIMIT = 100;

function key(e, code) {
    return (e.keyCode || e.which) === code
}

function unixtodate(unix) {
    return new Date(unix * 1000);
}

function calculateage(unix) {
    let today = new Date();
    let birthDate = unixtodate(unix);
    let age = today.getFullYear() - birthDate.getFullYear();
    let m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function calculatefeetinches(inches) {
    return ((inches - (inches % 12)) / 12) + "' " + (inches % 12) + "''";
}

function getmonthasword(number) {
    switch (number) {
        case 0:
            return "January";
        case 1:
            return "Feburary";
        case 2:
            return "March";
        case 3:
            return "April";
        case 4:
            return "May";
        case 5:
            return "June";
        case 6:
            return "July";
        case 7:
            return "August";
        case 8:
            return "September";
        case 9:
            return "October";
        case 10:
            return "November";
        case 11:
            return "December";
        default:
            return "Month";
    }
}

function getresultitemhtml(patient) {
    return "<div class=\"result\" patientid=\"" + patient["id"] + "\"><span>" + patient["last"] + ", " + patient["first"] + "</span><br><span>" + calculateage(patient["dob"]) + " years old</span></div>";
}

function getpatientcontenthtml(patient) {
    // src=\"https://thispersondoesnotexist.com/image?" + new Date().getTime()
    let utd = unixtodate(patient["dob"]);
    let html = "<div class=\"ataglance\"><img alt=\"picture\" src=\"https://thispersondoesnotexist.com/image?" + new Date().getTime() + "\"><div class=\"name\">" + patient["first"] + " " + patient["last"] + "</div><div class=\"age\">" + calculateage(patient["dob"]) + " years old</div><div class=\"dateofbirth\">Born on " + getmonthasword(utd.getMonth()) + " " + utd.getDate() + ", " + utd.getFullYear() + "</div><div class=\"weight\">" + patient["weight"] + " lbs</div><div class=\"height\">" + calculatefeetinches(patient["height"]) + "</div></div><div class=\"content-list\"><br><span>Conditions</span><ul>";

    if (patient["conditions"].length === 0) {
        html += "<li>None</li>"
    } else {
        for (let i = 0; i < patient["conditions"].length; i++) {
            html += "<li>" + patient["conditions"][i] + "</li>"
        }
    }

    html += "</ul></div><div class=\"content-list\"><span>Family Members</span><ul><li>NAME (REALTIONSHIP)</li></ul></div><div class=\"content-list\"><span>Medications</span><ul>";

    if (patient["meds"].length === 0) {
        html += "<li>None</li>"
    } else {
        for (let i = 0; i < patient["meds"].length; i++) {
            html += "<li>" + patient["meds"][i] + "</li>"
        }
    }

    html += "</ul></div><div class=\"content-list\"><span>Notes</span><br><textarea style=\"width:400px;height:128px\">" + patient["notes"] + "</textarea></div>";

    return html
}

function pushresults(data, type) {
    clearresulthtml();
    let noresults = true;
    let list = $("div.actuallist")[0];
    for (let i = 0; i < data.length; i++) {
        if (i > RESULT_LIMIT) {
            break;
        }
        let p = data[i]["data"];
        if (p[searchtypes[type]].substring(0, prev.length).toLowerCase() === prev.toLowerCase()) {
            list.innerHTML += getresultitemhtml(p);
            noresults = false;
        }
    }
    if (noresults) {
        $("div.noresultstext").show()
    } else {
        registerclickevents()
    }
}

function pushpatientcontent(id) {
    $.getJSON("index/id/" + id.toString().substring(0, 1) + ".json", function (data) {
        for (let i = 0; i < data.length; i++) {
            let p = data[i]["data"];
            if (p["id"].toString() === id.toString()) {
                $("div.rightpane div")[0].innerHTML = getpatientcontenthtml(p);
                break;
            }
        }
    });
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
    let letters = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
    for (let i = 0; i < letters.length; i++) {
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

function searchtypechange() {
    prev = prev + prev;
    if (prev.length > 1000) {
        prev = "";
    }
}

function registerclickevents() {
    let resultitems = $("div.result");
    resultitems.off("click");
    resultitems.on("click", function () {
        pushpatientcontent($(this).attr("patientid"))
    })
}

$(document).ready(function () {
    $("input.searchbar").on("keyup", function (e) {
        if (key(e, 13)) {
            // pressed enter
            let t = TYPE_FIRSTNAMES;
            if ($("input#lastnameradio").is(":checked")) {
                t = TYPE_LASTNAMES;
            }
            // start search
            search($("input.searchbar").val(), t)
        }
    });
    $('input#firstnameradio').change(function () {
        if ($(this).is(':checked')) {
            $("input#lastnameradio").prop("checked", false)
        }
        searchtypechange()
    });
    $('input#lastnameradio').change(function () {
        if ($(this).is(':checked')) {
            $("input#firstnameradio").prop("checked", false)
        }
        searchtypechange()
    });
    $("button#clearresults").on("click", function (e) {
        clearresulthtml();
        hideall();
        $("div.starttext").show();
        $("input.searchbar").val("");
    });

    hideall();
    $("div.starttext").show()
});