(function () {
  if (typeof acf === "undefined") {
    return;
  }

  acf.addAction("prepare", function () {
    const cloneEditors = document.querySelectorAll(
      ".acf-row.-clone .acf-editor-wrap textarea",
    );

    cloneEditors.forEach(function (textarea) {
      textarea.removeAttribute("id");
    });
  });
})();
