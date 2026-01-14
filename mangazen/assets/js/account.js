/**
 * User Account JavaScript
 * Handles login, register, and profile forms
 */
(function ($) {
  "use strict";

  // Toggle password visibility
  $(document).on("click", ".toggle-password", function () {
    var input = $(this).siblings("input");
    var icon = $(this).find("i");

    if (input.attr("type") === "password") {
      input.attr("type", "text");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      input.attr("type", "password");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });

  // Show message helper
  function showMessage(container, message, type) {
    var $container = $(container);
    $container
      .removeClass("success error")
      .addClass(type)
      .html('<i class="fas fa-' + (type === "success" ? "check-circle" : "exclamation-circle") + '"></i> ' + message)
      .slideDown();
  }

  // Set button loading state
  function setLoading($btn, loading) {
    if (loading) {
      $btn.addClass("loading").prop("disabled", true);
    } else {
      $btn.removeClass("loading").prop("disabled", false);
    }
  }

  // Login Form
  $("#login-form").on("submit", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $btn = $form.find(".btn-submit");
    var $message = $("#login-message");

    $message.slideUp();
    setLoading($btn, true);

    $.ajax({
      url: komikAccount.ajaxUrl,
      type: "POST",
      data: {
        action: "komik_login",
        nonce: komikAccount.nonce,
        username: $form.find('[name="username"]').val(),
        password: $form.find('[name="password"]').val(),
        remember: $form.find('[name="remember"]').is(":checked") ? "true" : "false",
      },
      success: function (response) {
        if (response.success) {
          showMessage($message, response.data.message, "success");
          setTimeout(function () {
            window.location.href = response.data.redirect;
          }, 1000);
        } else {
          showMessage($message, response.data.message, "error");
          setLoading($btn, false);
        }
      },
      error: function () {
        showMessage($message, "Connection error. Please try again.", "error");
        setLoading($btn, false);
      },
    });
  });

  // Register Form
  $("#register-form").on("submit", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $btn = $form.find(".btn-submit");
    var $message = $("#register-message");
    var password = $form.find('[name="password"]').val();
    var passwordConfirm = $form.find('[name="password_confirm"]').val();

    // Client-side validation
    if (password !== passwordConfirm) {
      showMessage($message, "Passwords do not match.", "error");
      return;
    }

    if (password.length < 6) {
      showMessage($message, "Password must be at least 6 characters.", "error");
      return;
    }

    $message.slideUp();
    setLoading($btn, true);

    $.ajax({
      url: komikAccount.ajaxUrl,
      type: "POST",
      data: {
        action: "komik_register",
        nonce: komikAccount.nonce,
        username: $form.find('[name="username"]').val(),
        email: $form.find('[name="email"]').val(),
        password: password,
        password_confirm: passwordConfirm,
      },
      success: function (response) {
        if (response.success) {
          showMessage($message, response.data.message, "success");
          setTimeout(function () {
            window.location.href = response.data.redirect;
          }, 1000);
        } else {
          showMessage($message, response.data.message, "error");
          setLoading($btn, false);
        }
      },
      error: function () {
        showMessage($message, "Connection error. Please try again.", "error");
        setLoading($btn, false);
      },
    });
  });

  // Profile Tabs
  $(".profile-nav .nav-item[data-tab]").on("click", function (e) {
    e.preventDefault();

    var tabId = $(this).data("tab");

    // Update nav
    $(".profile-nav .nav-item").removeClass("active");
    $(this).addClass("active");

    // Update content
    $(".profile-tab").removeClass("active");
    $("#" + tabId).addClass("active");

    // Update URL hash
    window.location.hash = tabId;
  });

  // Check URL hash on load
  if (window.location.hash) {
    var hash = window.location.hash.substring(1);
    var $tab = $('.profile-nav .nav-item[data-tab="' + hash + '"]');
    if ($tab.length) {
      $tab.trigger("click");
    }
  }

  // Update Profile Form
  $("#profile-form").on("submit", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $btn = $form.find(".btn-submit");
    var $message = $("#profile-message");

    $message.slideUp();
    setLoading($btn, true);

    $.ajax({
      url: komikAccount.ajaxUrl,
      type: "POST",
      data: {
        action: "komik_update_profile",
        nonce: komikAccount.nonce,
        display_name: $form.find('[name="display_name"]').val(),
        email: $form.find('[name="email"]').val(),
        bio: $form.find('[name="bio"]').val(),
      },
      success: function (response) {
        if (response.success) {
          showMessage($message, response.data.message, "success");
          // Update displayed name
          $(".profile-name").text($form.find('[name="display_name"]').val());
        } else {
          showMessage($message, response.data.message, "error");
        }
        setLoading($btn, false);
      },
      error: function () {
        showMessage($message, "Connection error. Please try again.", "error");
        setLoading($btn, false);
      },
    });
  });

  // Update Password Form
  $("#password-form").on("submit", function (e) {
    e.preventDefault();

    var $form = $(this);
    var $btn = $form.find(".btn-submit");
    var $message = $("#password-message");
    var newPass = $form.find('[name="new_password"]').val();
    var confirmPass = $form.find('[name="confirm_password"]').val();

    if (newPass !== confirmPass) {
      showMessage($message, "New passwords do not match.", "error");
      return;
    }

    $message.slideUp();
    setLoading($btn, true);

    $.ajax({
      url: komikAccount.ajaxUrl,
      type: "POST",
      data: {
        action: "komik_update_password",
        nonce: komikAccount.nonce,
        current_password: $form.find('[name="current_password"]').val(),
        new_password: newPass,
        confirm_password: confirmPass,
      },
      success: function (response) {
        if (response.success) {
          showMessage($message, response.data.message, "success");
          $form[0].reset();
        } else {
          showMessage($message, response.data.message, "error");
        }
        setLoading($btn, false);
      },
      error: function () {
        showMessage($message, "Connection error. Please try again.", "error");
        setLoading($btn, false);
      },
    });
  });

  // Avatar Upload
  $("#change-avatar-btn").on("click", function () {
    $("#avatar-input").trigger("click");
  });

  $("#avatar-input").on("change", function () {
    var file = this.files[0];
    if (!file) return;

    // Validate file type
    var allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    if (allowedTypes.indexOf(file.type) === -1) {
      alert("Please select a valid image file (JPG, PNG, GIF, or WebP).");
      return;
    }

    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert("File too large. Maximum size is 2MB.");
      return;
    }

    var formData = new FormData();
    formData.append("action", "komik_update_avatar");
    formData.append("nonce", komikAccount.nonce);
    formData.append("avatar", file);

    var $avatar = $(".profile-avatar");
    $avatar.addClass("uploading");

    $.ajax({
      url: komikAccount.ajaxUrl,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $avatar.removeClass("uploading");
        if (response.success) {
          $(".profile-avatar img").attr("src", response.data.avatar_url);
        } else {
          alert(response.data.message);
        }
      },
      error: function () {
        $avatar.removeClass("uploading");
        alert("Upload failed. Please try again.");
      },
    });
  });

  // Remove Bookmark from Profile Page
  $(document).on("click", ".remove-bookmark", function () {
    var $btn = $(this);
    var manhwaId = $btn.data("id");
    var $item = $btn.closest(".bookmark-item");

    if (!confirm("Remove this bookmark?")) {
      return;
    }

    $btn.prop("disabled", true);

    $.ajax({
      url: komikAccount.ajaxUrl,
      type: "POST",
      data: {
        action: "komik_remove_bookmark",
        nonce: komikAccount.nonce,
        manhwa_id: manhwaId,
      },
      success: function (response) {
        if (response.success) {
          $item.fadeOut(300, function () {
            $(this).remove();
            // Update bookmark count
            var currentCount = parseInt($(".stat-item .stat-value").first().text()) || 0;
            $(".stat-item .stat-value")
              .first()
              .text(Math.max(0, currentCount - 1));
            // Check if no more bookmarks
            if ($(".bookmark-item").length === 0) {
              $(".bookmark-grid").html('<div class="empty-state">' + '<i class="fas fa-bookmark"></i>' + "<h3>No bookmarks yet</h3>" + "<p>Bookmark your favorite manhwa to find them easily!</p>" + "</div>");
            }
          });
        } else {
          alert(response.data.message);
          $btn.prop("disabled", false);
        }
      },
      error: function () {
        alert("Failed to remove bookmark. Please try again.");
        $btn.prop("disabled", false);
      },
    });
  });
})(jQuery);
