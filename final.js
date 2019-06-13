let prev = "";
let searchtypes = ["first", "last"];
let searchtype = 0;
let lastactiveresultitem = null;
const TYPE_FIRSTNAMES = 0;
const TYPE_LASTNAMES = 1;
const RESULT_LIMIT = 100;
const TEST_USERNAME = "username";
const TEST_PASSWORD = "password";

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

function getprettyheight(inches) {
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
    return "<div class=\"result\" patientid=\"" + patient["id"] + "\"><span>" + patient["last"] + ", " + patient["first"] + "</span><br><span>#" + patient["id"] + "</span></div>";
}

function getpatientcontenthtml(patient) {
    // src=\"https://thispersondoesnotexist.com/image?" + new Date().getTime()
    let utd = unixtodate(patient["dob"]);
    let html = "<div class=\"ataglance\"><img alt=\"picture\" src=\"blank.png\"><div class=\"name\">" + patient["first"] + " " + patient["last"] + "</div><div class=\"age\">";
    if (Math.random() * 10 > 5) {
        html += "Female";
    } else {
        html += "Male";
    }
    html += ", " + calculateage(patient["dob"]) + " years old</div><div class=\"dateofbirth\">Born on " + getmonthasword(utd.getMonth()) + " " + utd.getDate() + ", " + utd.getFullYear() + "</div><div class=\"weight\">" + patient["w"] + " lbs</div><div class=\"height\">" + getprettyheight(patient["h"]) + "</div></div><div class=\"content-list\"><br><span>Conditions</span><ul>";

    if (patient["conditions"].length === 0) {
        html += "<li>None</li>"
    } else {
        for (let i = 0; i < patient["conditions"].length; i++) {
            html += "<li>" + patient["conditions"][i] + "</li>"
        }
    }

    html += "</ul></div><div class=\"content-list\"><span>Medications</span><ul>";

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
    clearresultitems();
    let noresults = true;
    let list = $("div.actuallist")[0];
    for (let i = 0; i < data.length; i++) {
        if (i > RESULT_LIMIT) {
            break
        }
        let p = data[i]["data"];
        if (p[searchtypes[type]].substring(0, prev.length).toLowerCase() === prev.toLowerCase()) {
            list.innerHTML += getresultitemhtml(p);
            noresults = false
        }
    }
    if (noresults) {
        $("div.noresultstext").show()
    } else {
        registeronclickresultitems()
    }
}

function onresultitemclick(id) {
    if (lastactiveresultitem !== null) {
        lastactiveresultitem.removeClass("active")
    }
    $.getJSON("index/id/" + id.toString().substring(0, 3) + ".json", function (data) {
        for (let i = 0; i < data.length; i++) {
            let p = data[i]["data"];
            if (p["id"].toString() === id.toString()) {
                $("div.rightpane div")[0].innerHTML = getpatientcontenthtml(p);
                break
            }
        }
        // ryan's "h y p e r l i n k" bs
        let hyperlink = $("div.rightpane div.ataglance div.name");
        hyperlink.off("click");
        hyperlink.on("click", function () {
            // set search bar text to their last name
            $("input.searchbar").val(hyperlink.html().substring(hyperlink.html().indexOf(" ") + 1));
            // make sure search type is last name
            $("button#f").removeClass("clicked");
            $("button#l").addClass("clicked");
            onsearchtypechanged();
            searchtype = TYPE_LASTNAMES;
            // simulate pressing enter
            onenter()
        })
    });
}

function search(query, type) {
    $("div.starttext").hide();
    query = query.toLowerCase();
    if (!query.match(/^[A-Za-z]+$/)) {
        $("div.invalidtext").show();
        $("div.actuallist").hide();
        prev = query;
        return
    } else if (query === prev) {
        return
    }

    hideall();
    $("div.loadingthingy").show();
    $("div.loadingthingy img").attr("src", "spinner_large.svg");

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

function clearresultitems() {
    $("div.actuallist").html("")
}

function hideall() {
    $("div.loadingthingy").hide();
    $("div.noresultstext").hide();
    $("div.invalidtext").hide();
    $("div.starttext").hide();
    $("div.actuallist").hide();
}

function togglesearchtype() {
    if (searchtype === TYPE_FIRSTNAMES) {
        searchtype = TYPE_LASTNAMES
    } else {
        searchtype = TYPE_FIRSTNAMES
    }
}

function onsearchtypechanged() {
    togglesearchtype();
    prev = prev + prev;
    if (prev.length > 1000) {
        prev = "";
    }
}

function registeronclickresultitems() {
    let resultitems = $("div.result");
    resultitems.off("click");
    resultitems.on("click", function () {
        onresultitemclick($(this).attr("patientid"));
        lastactiveresultitem = $(this);
        lastactiveresultitem.addClass("active")
    })
}

function onenter() {
    search($("input.searchbar").val(), searchtype)
}

$(document).ready(function () {
    $.ajaxSetup({cache: false});

    $("input.searchbar").on("keyup", function (e) {
        if (key(e, 13)) {
            onenter()
        }
    });
    $("input#p").on("keyup", function (e) {
        if (key(e, 13)) {
            $("div.lock button").click()
        }
    });
    $('button#f').on("click", function () {
        console.log("firstname");
        $(this).addClass("clicked");
        $("button#l").removeClass("clicked");
        onsearchtypechanged()
    });
    $('button#l').on("click", function () {
        console.log("lastname");
        $(this).addClass("clicked");
        $("button#f").removeClass("clicked");
        onsearchtypechanged()
    });
    $("button#clearresults").on("click", function () {
        clearresultitems();
        hideall();
        $("div.starttext").show();
        $("input.searchbar").val("")
    });
    $("div.lock button").on("click", function () {
        if ($("input#p").val() === TEST_PASSWORD && $("input#u").val() === TEST_USERNAME) {
            $.when($("div.lock").fadeOut("fast")).done(function () {
                $("div.app").fadeIn("fast")
            })
        }
    });

    hideall();
    $("button#f").addClass("clicked");
    $("div.starttext").show();
    $("div.app").hide();
    $("div.lock").show()
});