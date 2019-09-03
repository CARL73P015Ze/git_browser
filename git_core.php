<?php

// todo move this into a config file
$repo_path = '/home/development/';
$repos = array();
$repos['gitrepo/'] = 'gitrepo';
$repos['android_studio/'] = 'android';
$repos['android/game/'] = 'game';

$selected_repo = array_keys($repos)[0];

// just split the commit message
function process_commit($commit){
  $columns = explode(chr(0x01), $commit);
  $datetext = '';
  $author = '';
  $msg = '';
  $cnt = count($columns);
  if($cnt > 2){
    $datetext = $columns[0]; //0 = datetime
    $author = $columns[1]; //1 = author
    $msg = $columns[2];//2 = msg
  }

  return ['date' => $datetext, 'author'=>$author, 'message' => $msg];
}

function git_branches($repo){
  $repo = escapeshellarg($repo);
  $git = 'git -C ' . $repo .' branch';

  exec($git, $branches);

  for($i=0; $i<count($branches); $i++){
      $branches[$i] = substr($branches[$i], 2);
  }
  return $branches;
}

function git_recent_commits($repo, 
                        $branch_name, 
                        $search_term, 
                        $offset, 
                        $since, 
                        $until){
    if($branch_name != '')
     $branch_name = escapeshellarg($branch_name);
 
    if($search_term != '')
     $search_term = ' -i --grep=' . escapeshellarg($search_term);
 
    $repo = escapeshellarg($repo);
    
    if($since != '')
      $since = ' --since=' . $since . ' ';
    
    if($until != '')
      $until = ' --until=' .$until . ' ';

    $git = 'git -C '.$repo.' log ' . $branch_name . ' --skip='.$offset.' --max-count=25 ' . 
              $search_term . $since . $until .
              ' --date=format:"  %Y-%m-%d" --pretty=format:"%ad%x01%cn%x01%s"';

    exec($git, $commits);
   
    for($i=0; $i<count($commits); $i++){
        $commits[$i] = substr($commits[$i], 2);
    }

   return $commits;
 }
 