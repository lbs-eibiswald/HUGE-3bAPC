/**
 * Show or Hide an element by giving @id of the element
 * @param divID 
 */
function toggleVisibility(divID) {
    const div = document.getElementById(divID);
    div.style.display = (div.style.display === "none" || div.style.display === "") ? "flex" : "none";
}