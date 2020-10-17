var OJ_VERDICT = new Array(
    "Pending",
    "Pending Rejudge",
    "Compiling",
    "Running & Judging",
    "Accepted",
    "Presentation Error",
    "Wrong Answer",
    "Time Limit Exceeded",
    "Memory Limit Exceeded",
    "Output Limit Exceeded",
    "Runtime Error",
    "Compile Error",
    "System Error",
    "No Test Data"
);
// bootstrap 3 CSS class
var OJ_VERDICT_COLOR = new Array(
    "text-muted",
    "text-muted",
    "text-muted",
    "text-muted",
    "text-success", // AC
    "text-warning", // PE
    "text-danger",  // WA
    "text-warning", // TLE
    "text-warning", // MLE
    "text-warning", // OLE
    "text-warning", // RE
    "text-warning", // CE
    "text-danger",  // SE
    "text-danger"
);
function testHtml(id, caseJsonObject)
{
  if(OJ_VERDICT[caseJsonObject.verdict]==OJ_VERDICT[4]) {
  return '<div class="panel panel-default test-for-popup"> \
        <div class="panel-heading" role="tab" id="heading' + id + '"> \
        <b class="text-success">  \
                    测试点' + id + ' \
                    : ' + OJ_VERDICT[caseJsonObject.verdict] + ', \
                    时间: ' + caseJsonObject.time + ' 毫秒, \
                    内存: ' + caseJsonObject.memory + ' KB \
                </b> \
        </div>';
  }else{
    return '<div class="panel panel-default test-for-popup"> \
        <div class="panel-heading" role="tab" id="heading' + id + '"> \
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" \
                   href="#test-' + id + '" aria-expanded="false" aria-controls="test-' + id + '"><b class=" text-danger">  \
                    测试点' + id + ' \
                    : ' + OJ_VERDICT[caseJsonObject.verdict] + ', \
                    时间: ' + caseJsonObject.time + ' 毫秒, \
                    内存: ' + caseJsonObject.memory + ' KB \
         </b></a> \
        </div> \
        <div id="test-' + id + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading' + id + '"> \
            <div class="panel-body">\
                <div class="sample-test">\
                    <div class="input">\
                        <b>&nbsp;输入数据</b>\
                        <pre>' + caseJsonObject.input + '</pre>\
                    </div>\
                    <div class="output">\
                        <b>&nbsp;标准答案</b>\
                        <pre>' + caseJsonObject.output + '</pre>\
                    </div>\
                    <div class="output">\
                        <b>&nbsp;你的答案</b>\
                        <pre>' + caseJsonObject.user_output + '</pre>\
                    </div>\
                    <div class="output">\
                        <b>&nbsp;检查日志</b>\
                        <pre>' + caseJsonObject.checker_log + '</pre>\
                    </div>\
                    <div class="output">\
                        <b>&nbsp;系统信息</b>\
                        <pre>exit code: ' + caseJsonObject.exit_code + ', checker exit code: ' + caseJsonObject.checker_exit_code + '</pre>\
                    </div>\
                </div>\
            </div>\
        </div>\
    </div>';
	}
}

function subtaskHtml(id, score, verdict)
{
  var scoregot = score;
  var csscolor = 'panel-success';
  if (verdict != 4) {
    scoregot = 0;
    csscolor = 'panel-warning';
  }
  return '<div class="panel ' + csscolor + ' test-for-popup"> \
        <div class="panel-heading" role="tab" id="subtask-heading-' + id + '"> \
            <h4 class="panel-title"> \
                <a role="button" data-toggle="collapse" \
                    href="#subtask-' + id + '" aria-expanded="false" aria-controls="subtask-' + id + '"> \
                    子任务 #' + id + ', 分数: ' + score + ', 得分: ' + scoregot + ' \
                </a> \
            </h4> \
        </div> \
        <div id="subtask-' + id + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="subtask-heading-' + id + '"> \
            <div id="subtask-body-' + id + '" class="panel-body"> \
            </div> \
        </div> \
    </div>';
}
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
    $('.pre p').each(function(i, block) {  // use <pre><p>
      hljs.highlightBlock(block);
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
