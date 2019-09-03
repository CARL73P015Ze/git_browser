<!DOCTYPE html>
<html>
<head>
<link href="jquery-ui/jquery-ui.css" rel="stylesheet">

<script src="jquery.js"></script>
<script src="jquery-ui/jquery-ui.js"></script>

<style>
body { background-color: white; font-family:sans; font-size:14px; } 
a {font-weight: bold;}
.email {color: black;}
.commit_date {font-weight: bold; font-size: 12px; color: #586069; }
.commit_author {font-size: 12px; color: #586069; }
li { 
  list-style: none;
  background-color: white; 
  margin: 5px; 
  padding: 5px; 
  border: 1px; 
  border-color: #eaecef; 
  border-style: solid;
  }
li:hover { background-color: #f6fbff;  }

.commit_msg {color: #586069;}
.commit_revert {font-weight: bold; color: red;}
.commit_build {font-weight: bold; color: green;}
.commit_id {font-weight: bold; color: black;}
.search_match {background-color: orange;}
.end_of_results {color: black; text-align: center;}
.div_column {display: table-cell;}
.div_column_mid {width: 100%;
  padding-left: 20px;
  padding-right: 20px;
}
h1 {color: #7278bb;}
.div_last {text-align: right; width: 200px;}
.btnUntil { background: green; padding: 0.2em; border:1px;}
.btnUntil:hover { background: yellow; }
.btnSince { background: red;  padding: 0.2em; border:1px;}
.btnSince:hover { background: yellow; }
</style>

</head>

<script>
function setSince(obj){
  $('#since').val(obj.data('dt'));
};

function setUntil(obj){
  $('#until').val(obj.data('dt'));
};

function mapJSONCommitToHtml(json){
  var eof = false;
  if(json.length == 25){
    var offset = $('#next').data('offset');
    $('#next').data('offset', offset+json.length);
    $('#next').data('eof', false);
  }else{
    // remove scroller
    $('#next').data('offset', json.length);
    $('#next').data('eof', true);
    eof = true;
  }
  
  var i = 0;

  while(i < json.length){
    var dt = '<span class="commit_date">' + json[i].date + '</span>';
    var author = '<span class="commit_author">' + json[i].author + '</span>';

    var msg = json[i].message;

    msg = msg.replace(/((.*)(REVERT)(.*))/i, function(match){
      return '<span class="commit_revert">'+match+'</span>';
    });
  
    msg = msg.replace(/((BUILD.{0,7}([0-9]+\.){1,4}).*)/i, function(match){
      return '<span class="commit_build">'+match+'</span>';
    });

    msg = msg.replace(/(CF[0-9]+)/i, function(match){
      return '<span class="commit_id">'+match+'</span>';
    });
    
    var lside = '<div class="div_column" style="width:100px;" >'  + dt +'<br />'+ author +  '</div>';
    var mside = '<div class="div_column div_column_mid"><p>'  + msg +  '</p></div>';
    var rside = '<div class="div_column div_last">'+
                '<a class="btnSince" onclick="setSince($(this))" data-dt="'+json[i].date +'" href="#"> since </a><br />'+
                '<a class="btnUntil" onclick="setUntil($(this))" data-dt="'+json[i].date +'" href="#"> until </a></div>';

                
    $('#gitlogs').append('<li>' + lside +  mside + rside + '</li>');
    i = i + 1;
  }

  if(eof){
    $('#gitlogs').append('<li class="end_of_results">-----End Of Results-----</li>');
  }
}

function getFirstData(){
  $.getJSON("git_api.php", {
    "cmd": "search",
    "repo": $('#repo').val(), 
    "branch": $('#branch').val(), 
    "since": $('#since').val(), 
    "until": $('#until').val(), 
    "search_term": $('#search_term').val()}, 
    function(json){
        $('#gitlogs').text("");
        mapJSONCommitToHtml(json);
    });
};

function getNextData(){
  if($('#next').data('eof') == true)
    return;

  $.getJSON("git_api.php", {
    "cmd": "search",
    "repo": $('#repo').val(), 
    "branch": $('#branch').val(), 
    "since": $('#since').val(), 
    "until": $('#until').val(), 
    "search_term": $('#search_term').val(),
    "offset": $('#next').data('offset')}, 
  function(json){
    mapJSONCommitToHtml(json);
  });
};

function getBranches(){
  $.getJSON("git_api.php",{"cmd": "branch",
                        "repo": $('#repo').val() }, 
                        function(json){

                          var i = 0;
                          var html = "";
                          var selected_option = "selected";
                          while(i < json.length){
                            html += '<option value="' + json[i] + '" ' + 
                                      selected_option + ' >' + json[i] + 
                                      '</option>';
                            selected_option = "";
                            i++;
                          }
                          $('#branch').html(html);

                        });

    $('#branch').prop("selectedIndex", 0);
}
$( function() {

  $("#search_term").keyup(function(){
    getFirstData();
  });

  $("#repo").change(function(){
    getBranches();
    getFirstData();
  });

  $("#branch").change(function(){
    getFirstData();
  });
} );

$( document ).ready(function() {
  $( ".datepicker" ).datepicker({"dateFormat": "yy-mm-dd"});
  getBranches();
  getFirstData();
});
</script>
<body>
<form>

<label>Repository</label>
<select name="repo" id="repo">
    <?php 
    include "git_core.php";
    foreach ($repos as $key => $r) {
      $selected_repo  = '';
      if(selected_repo == $key)
        $selected_repo = 'selected';
      echo '<option value="' . htmlentities($key) . '" '.$selected_option.' >' . htmlentities($r) .'</option>';
    }
    ?>
    <option value="no" >no</option>
  </select>
<label for="since">Branch</label>
  <select  name="branch" id="branch">
  </select>
  <label for="search_term">Search</label>
  <input type="text" id="search_term" name="search_term" />

    <fieldset>
      <legend>Date Range</legend>
      <label for="since">since</label>
      <input type="text" class="datepicker" id="since" name="since" />
      <label for="until">until</label>
      <input type="text" class="datepicker" id="until" name="until" />
    </fieldset>
  </form>

  <ul id="gitlogs">
  </ul>


<span id="next">
<script>
$( window ).scroll(function() {
  if($(window).scrollTop() == $(document).height() - $(window).height()) {
    getNextData();
  }
});
</script>

</body>
</html>
