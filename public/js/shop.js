/**
 * Show or Hide an element by giving @id of the element
 * @param divID 
 */
function toggleVisibility(divID) {
    const div = document.getElementById(divID);
    div.style.display = (div.style.display === "none" || div.style.display === "") ? "flex" : "none";
}

function switchVisibility(firstDiv, secondDiv, displayType = "block") {
    const fDiv = document.getElementById(firstDiv);
    const sDiv = document.getElementById(secondDiv);

    const firstIsVisible = window.getComputedStyle(fDiv).display !== "none";
    fDiv.style.display = firstIsVisible ? "none" : displayType;
    sDiv.style.display = firstIsVisible ? displayType : "none";
}