// Rename 'body' to something more specific to avoid conflict with document.body
const pageBody = document.getElementsByTagName("body")[0];

// Left sidebar toggle
function collapseSidebar() {
    pageBody.classList.toggle("leftsidebar-expand");
}

// Topnavbar dropdown function
window.onclick = function (event) {
    openCloseDropdown(event);
};

function closeAllDropdown() {
    var dropdowns = document.getElementsByClassName("mydropdown-expand");
    for (var i = 0; i < dropdowns.length; i++) {
        dropdowns[i].classList.remove("mydropdown-expand");
    }
}

function openCloseDropdown(event) {
    if (!event.target.matches(".mydropdown-toggle")) {
        closeAllDropdown();
    } else {
        var toggle = event.target.dataset.toggle;
        var content = document.getElementById(toggle);
        if (content.classList.contains("mydropdown-expand")) {
            closeAllDropdown();
        } else {
            closeAllDropdown();
            content.classList.add("mydropdown-expand");
        }
    }
}
