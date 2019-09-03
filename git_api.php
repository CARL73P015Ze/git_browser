<?php
include "git_core.php";


function get_branchs($repo_path, $selected_repo, $repos){
  if(isset($_GET['repo'])){
    if(array_key_exists($_GET['repo'], $repos)){
      $selected_repo = $_GET['repo'];
    }
  }
  $branches = git_branches($repo_path . $selected_repo);
  echo json_encode($branches);
}

function get_search($repo_path, $selected_repo, $repos){
  if(isset($_GET['repo'])){
    if(array_key_exists($_GET['repo'], $repos)){
      $selected_repo = $_GET['repo'];
    }
  }

  $search_term = $_GET['search_term'];
  $selected_branch = $_GET['branch'];

  $offset = 0;
  if(isset($_GET['offset']) ) {
    $offset = (int)$_GET['offset'];
    if($offset < 0)
      $offset = 0;
  }

  $since = '';
  $until = '';

  if(isset($_GET['since']) ) {
    $since = trim($_GET['since']);
  }

  if(isset($_GET['until']) ) {
    $until = trim($_GET['until']);
  }

  $commits = git_recent_commits($repo_path . $selected_repo, 
                    $selected_branch, $search_term, $offset,
                    $since, $until);
  $json = array();

  foreach ($commits as $key => $commit) {
    $json[] = process_commit($commit);
    
  }
 
  echo json_encode($json);
}


if(isset($_GET['cmd'])){
  switch($_GET['cmd']){
    case "branch":
      get_branchs($repo_path, $selected_repo, $repos);
      break;
    case "search":  
      get_search($repo_path, $selected_repo, $repos);
      break;
    default:
      break;
  }
}