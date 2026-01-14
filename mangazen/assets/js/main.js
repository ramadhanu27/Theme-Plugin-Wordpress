/**
 * Komik Starter Theme - Main JavaScript
 *
 * @package Komik_Starter
 */

(function ($) {
  "use strict";

  // DOM Ready
  $(document).ready(function () {
    // NOTE: Mobile Menu Toggle (.shme) and Mobile Search Toggle (.srcmob)
    // are now handled in footer.php with native JS and touch support.
    // Do NOT add click handlers here to avoid double-toggle bug.

    // Close mobile menu on link click
    $(document).on("click", "#main-menu.shwx ul li a", function () {
      if ($(window).width() <= 890) {
        $("#main-menu").removeClass("shwx");
        $(".shme i").removeClass("fa-times").addClass("fa-bars");
      }
    });

    // Scroll to Top Button
    var scrollBtn = $(".scrollToTop");

    $(window).scroll(function () {
      if ($(this).scrollTop() > 300) {
        scrollBtn.fadeIn();
      } else {
        scrollBtn.fadeOut();
      }
    });

    scrollBtn.on("click", function (e) {
      e.preventDefault();
      $("html, body").animate({ scrollTop: 0 }, 500);
    });

    // Lazy Load Images
    if ("IntersectionObserver" in window) {
      var lazyImages = document.querySelectorAll("img[data-src]");

      var imageObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var image = entry.target;
            image.src = image.dataset.src;
            image.removeAttribute("data-src");
            imageObserver.unobserve(image);
          }
        });
      });

      lazyImages.forEach(function (image) {
        imageObserver.observe(image);
      });
    }

    // Smooth scroll for anchor links
    $('a[href^="#"]').on("click", function (e) {
      var target = $(this.getAttribute("href"));
      if (target.length) {
        e.preventDefault();
        $("html, body").animate(
          {
            scrollTop: target.offset().top - 80,
          },
          500
        );
      }
    });

    // Search form focus effect
    $("#s, .searchx #form #s").on("focus", function () {
      $(this).parent().addClass("focused");
    });

    $("#s, .searchx #form #s").on("blur", function () {
      $(this).parent().removeClass("focused");
    });

    // Add hover effect to series cards
    $(".bs .bsx").hover(
      function () {
        $(this).find(".limit img").css("transform", "scale(1.05)");
      },
      function () {
        $(this).find(".limit img").css("transform", "scale(1)");
      }
    );

    // Chapter list toggle (if needed)
    $(".show-more-chapters").on("click", function (e) {
      e.preventDefault();
      var $list = $(this).closest(".bxcl").find("ul");
      $list.toggleClass("expanded");
      $(this).text($list.hasClass("expanded") ? "Show Less" : "Show More");
    });

    // Keyboard navigation for reader
    if ($("#readerarea").length) {
      $(document).keydown(function (e) {
        // Left arrow or A key - previous
        if (e.keyCode === 37 || e.keyCode === 65) {
          var prevLink = $('.nextprev a[rel="prev"]').attr("href");
          if (prevLink) {
            window.location.href = prevLink;
          }
        }
        // Right arrow or D key - next
        if (e.keyCode === 39 || e.keyCode === 68) {
          var nextLink = $('.nextprev a[rel="next"]').attr("href");
          if (nextLink) {
            window.location.href = nextLink;
          }
        }
      });
    }

    // Auto-submit chapter selector
    $("#chapter-select, .slc select").on("change", function () {
      var selectedChapter = $(this).val();
      if (selectedChapter) {
        window.location.href = selectedChapter;
      }
    });

    // Initialize tooltips (if any)
    $("[data-tooltip]").hover(
      function () {
        var tooltip = $(this).data("tooltip");
        $(this).append('<span class="tooltip-text">' + tooltip + "</span>");
      },
      function () {
        $(this).find(".tooltip-text").remove();
      }
    );

    // Image error handling - show placeholder
    $("img").on("error", function () {
      $(this).attr("src", komikStarterVars.templateUrl + "/assets/images/placeholder.jpg");
    });

    // Add loaded class to images
    $("img").on("load", function () {
      $(this).addClass("loaded");
    });
  });

  // Window Load
  $(window).on("load", function () {
    // Remove page loader if present
    $(".page-loader").fadeOut();

    // Re-layout for masonry-like grids
    setTimeout(function () {
      $(window).trigger("resize");
    }, 100);
  });

  // Window Resize
  $(window).on("resize", function () {
    // Close mobile menu on resize to desktop
    if ($(window).width() > 890) {
      $("#main-menu").removeClass("shwx");
      $(".shme i").removeClass("fa-times").addClass("fa-bars");
    }
  });
})(jQuery);
