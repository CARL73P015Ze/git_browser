# git_browser
git browser in php, screenshot here screenshot.PNG


the file: "index.php"  contains the front end UI.

the file: "git_core.php" contains some configuration variables:

$repo_path = '/home/development/'; // the root path for all the repos

// some key=>value pairs, they key is the relative path to $repo_path and the value is the name displayed in the UI.
$repos['gitrepo/'] = 'gitrepo';    
$repos['android_studio/'] = 'android';
$repos['android/game/'] = 'game';

configure these to whatever you want, add more repos, etc. 
