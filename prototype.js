var prev = "";
var searchtypes = ["firstname", "lastname"];
let TYPE_FIRSTNAMES = 0;
let TYPE_LASTNAMES = 1;
let RESULT_LIMIT = 100;

function key(e, code) {
    return (e.keyCode || e.which) === code
}

function calculateage(unix) {
    var today = new Date();
    var birthDate = new Date(unix * 1000);
    console.log(birthDate.getFullYear());
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function getresultitemhtml(patient) {
    return "<div class=\"result\" patientid=\"" + patient["id"] + "\"><span>" + patient["lastname"] + ", " + patient["firstname"] + "</span><br><span>AGE years old</span></div>";
}

function getpatientcontenthtml(patient) {
    // src=\"https://thispersondoesnotexist.com/image?" + new Date().getTime()
    return "" +
        "       <div class=\"patientpicture\">\n" +
        "            <img alt=\"picture\" src=\"https://thispersondoesnotexist.com/image?" + new Date().getTime() + "\">\n" +
        "            <div class=\"name\">" + patient["firstname"] + " " + patient["lastname"] + "</div>\n" +
        "            <div class=\"age\">" + calculateage(patient["dob"]) + " years old</div>\n" +
        "            <div class=\"dateofbirth\">Born on MONTH DAY, YEAR</div>\n" +
        "            <div class=\"weight\">WEIGHT lbs</div>\n" +
        "            <div class=\"height\">FEET' INCHES''</div>\n" +
        "       </div>\n" +
        "       <div class=\"content-list\"><br><span>Conditions</span><ul><li>N/A</li></ul></div>\n" +
        "       <div class=\"content-list\"><span>Family Members</span><ul><li>NAME (REALTIONSHIP)</li></ul></div>\n" +
        "       <div class=\"content-list\"><span>Medications</span><ul><li>N/A</li></ul></div>\n" +
        "       <div class=\"content-list\"><span>Notes</span><br><textarea style=\"width:400px;height:128px\"></textarea></div>";
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
    } else {
        registerclickevents()
    }
}

function pushpatientcontent(id) {
    $.getJSON("index/id/" + id.toString().substring(0, 1) + ".json", function (data) {
        for (let i = 0; i < data.length; i++) {
            let p = data[i];
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

function registerclickevents() {
    $("div.result").off("click");
    $("div.result").on("click", function () {
        pushpatientcontent($(this).attr("patientid"))
    })
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