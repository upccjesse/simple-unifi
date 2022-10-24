function createTableEditButton(tableRow) {
    return createTableButton(tableRow, "btn-primary", "bi-pencil", "showEdit")
}

function createTableDeleteButton(tableRow) {
    return createTableButton(tableRow, "btn-danger", "bi-trash", "showDelete")
}

function createTableButton(tableRow, buttonClass, buttonIcon, buttonFunction) {
    return "<button type=button class='btn " + buttonClass + " btn-sm' onclick='" + buttonFunction + "(`" + JSON.stringify(tableRow) + "`)'><i class=\"bi " + buttonIcon + "\"></i></button>"
}

function setButtonPending(button, pendingText) {
    $(button).data("orig-html", $(button).html())
    $(button).html(pendingText)
    $(button).attr("disabled", "true")
}

function setButtonOriginal(button) {
    $(button).html($(button).data("orig-html"))
    $(button).removeData("orig-html")
    $(button).removeAttr("disabled")
}

function setButtonFail(button) {
    $(button).data("orig-class", $(button).attr("class"))
    $(button).attr("class", "btn btn-warning")
    setButtonOriginal(button)
    setButtonPending(button, "Fail...")
    setButtonOriginalTimeout(button, 750)
}

function setButtonSuccess(button) {
    $(button).data("orig-class", $(button).attr("class"))
    $(button).attr("class", "btn btn-success")
    setButtonOriginal(button)
    setButtonPending(button, "Success!")
    setButtonOriginalTimeout(button, 750)
}

function setButtonOriginalTimeout(button, timeout) {
    setTimeout(function() {
        setButtonOriginal(button)
        $(button).attr("class", $(button).data("orig-class"))
        $(button).removeData("orig-class")
    }, timeout)
}

function closeModalTimeout(modalDomId, timeout) {
    setTimeout(function() {
        $(modalDomId).modal("hide")
    }, timeout)
}

function getCheckedItemsInTable(cssSelector) {
    let output = []
    $(cssSelector).each(function () {
        output.push({
            "id": $(this).attr("id"),
            "checked": $(this).is(":checked") ? 1 : 0
        })
    })
    return output
}