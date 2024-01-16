$(document).ready(function () {

  function renderKatex() {
    $(".katex.math.inline").each(function () {
      var parent = $(this).parent()[0];
      if (parent.localName !== "code") {
        var texTxt = $(this).text();
        var el = $(this).get(0);
        try {
          katex.render(texTxt, el);
        } catch (err) {
          $(this).html("<span class=\'err\'>" + err);
        }
      } else {
        $(this).parent().text($(this).parent().text());
      }
    });
    $(".katex.math.multi-line").each(function () {
      var texTxt = $(this).text();
      var el = $(this).get(0);
      try {
        katex.render(texTxt, el, {displayMode: true})
      } catch (err) {
        $(this).html("<span class=\'err\'>" + err)
      }
    });
  }
  renderKatex();

  function addCopyBtn() {
    $(".sample-test h4").each(function() {
      var preId = ("id" + Math.random()).replace('.', '0');
      var cpyId = ("id" + Math.random()).replace('.', '0');

      $(this).parent().find("pre").attr("id", preId);
      var copy = $("<div title='Copy' data-clipboard-target='#" + preId + "' id='" + cpyId + "' class='btn-copy'>复制</div>");
      $(this).append(copy);

      var clipboard = new ClipboardJS('#' + cpyId, {
        text: function(trigger) {
          return document.querySelector('#' + preId).innerText;
        }
      });
      clipboard.on('success', function(e) {
        $('#' + cpyId).text("已复制");
        setTimeout(function() {
          $('#' + cpyId).text('复制');
        }, 500);
        e.clearSelection();
      });
      clipboard.on('error', function(e) {
        $('#' + cpyId).text("复制失败");
        setTimeout(function() {
          $('#' + cpyId).text('复制');
        }, 500);
      });
    });
  }
  addCopyBtn();
  $(document).on('pjax:complete', function() {
    renderKatex();
    addCopyBtn();
  });
//do something
})

$('pre').addClass("line-numbers").css("white-space", "pre-wrap");