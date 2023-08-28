let request = new XMLHttpRequest();

function requestData() {
    "use strict";
    request.open("GET", "news.php?JSON=1");
    request.onreadystatechange = processData;
    request.send(null);
}


function processData() {
    "use strict";
    if (request.readyState == 4) {
        if (request.status == 200) {
            if (request.responseText != null)
                processNews(request.responseText);
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } else;
}

function pollNews() {
    "use strict";
    // requestData();
    window.setInterval(requestData,3000);
}

function createDOM(row) {
    "use strict";

    let newsEntry = document.createElement("article");
    let title = document.createElement("h3");
    let textNodeTitle = document.createTextNode(row["title"]);
    title.appendChild(textNodeTitle);
    let time = document.createElement("p");
    time.className = "timestamp";
    let textNodeTime = document.createTextNode(row["timestamp"]);
    time.appendChild(textNodeTime);
    let text = document.createElement("p");
    let textNodeText = document.createTextNode(row["text"]);

    text.appendChild(textNodeText);
    newsEntry.appendChild(title);
    newsEntry.appendChild(time);
    newsEntry.appendChild(text);

    return newsEntry;
}

function processNews(data){
    "use strict";
    let obj = JSON.parse(data);
    // console.log(data);
    let newsSection = document.getElementById("newsSection");

    while (newsSection.firstChild) {
        newsSection.removeChild(newsSection.lastChild);
    }

    for (let i = 0; i < obj.length; i++) {
        let row = obj[i];
        // let newsEntries = createDOM(obj[i]);
        newsSection.appendChild(createDOM(row));
    }
    //
    // console.log(newsEntries);
    //
    //
    // newsSection.appendChild(newsEntries);
}
