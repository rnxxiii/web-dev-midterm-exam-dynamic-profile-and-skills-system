// app.js — ANSWER KEY
// TASK 7: $(document).ready()
$(document).ready(function () {
  // TASK 8: $('#feedbackForm').on('submit', ...)
  $("#feedbackForm").on("submit", function (event) {
    // TASK 9: event.preventDefault() + $(this).serialize()
    event.preventDefault();
    var formData = $(this).serialize();
    var form = this;

    // Reset response message before each submission
    $("#responseMsg").hide().removeClass("success error").text("");

    // TASK 10-12: $.ajax({ url, type, data })
    $.ajax({
      url: "process.php", // TASK 11
      type: "POST", // TASK 12
      data: formData,
      dataType: "json",
      // TASK 13: success callback parameter named 'response'
      success: function (response) {
        var msgDiv = $("#responseMsg");
        // TASK 14: response.status === 'success', response.message
        if (response.status === "success") {
          msgDiv.removeClass("error").addClass("success");
          msgDiv.text("✅ " + response.message);
          form.reset(); // Clear form fields after successful submission
        } else {
          msgDiv.removeClass("success").addClass("error");
          msgDiv.text("❌ " + response.message);
        }
        msgDiv.fadeIn();
      },
      error: function () {
        $("#responseMsg")
          .removeClass("success").addClass("error")
          .text("❌ Something went wrong. Please try again.")
          .fadeIn();
      }
    }); // end $.ajax
  }); // end form submit
}); // end document.ready