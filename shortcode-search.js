document.addEventListener("DOMContentLoaded", function() {
  // Überprüfe, ob sich die Seite in der gewünschten URL befindet
  if (window.location.href.indexOf('shortcode_search') > -1) {
    var dropdown = document.getElementById("shortcode-dropdown");

    // Überprüfe, ob das Dropdown-Element vorhanden ist, bevor du weitermachst
    if (dropdown) {
      function showSelectedPages() {
        var selectedShortcode = dropdown.options[dropdown.selectedIndex].value;
        var rows = document.querySelectorAll(".shortcode-table-row");
        rows.forEach(function(row) { row.style.display = "none"; });
        if (selectedShortcode) {
          var selectedRow = document.getElementById("shortcode-row-" + selectedShortcode);
          if (selectedRow) { selectedRow.style.display = "table-row"; }
        }
      }

      dropdown.addEventListener("change", showSelectedPages);
    }
  }
});

