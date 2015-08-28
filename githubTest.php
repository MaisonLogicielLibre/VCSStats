<?php
require_once("GithubApi.php");
$api= new GithubApi;
?>

<!DOCTYPE html>
<html>
<body>

<h1> Repository</h1>

<h2> Commits </h2>

<?php
    echo $api->getRepositoryCommits("KnpLabs","php-github-api");
?>

<h2> Contributors </h2>

<?php
echo $api->getRepositoryContributors("KnpLabs","php-github-api");
?>

<h2> Pull requests</h2>

Open :<?php echo $api->getRepositoryPullRequests("KnpLabs","php-github-api","open"); ?> <br>
Closed :<?php echo $api->getRepositoryPullRequests("KnpLabs","php-github-api","closed"); ?>

<h2> Issues</h2>

Open :<?php echo $api->getRepositoryIssues("KnpLabs","php-github-api","open"); ?> <br>
Closed :<?php echo $api->getRepositoryIssues("KnpLabs","php-github-api","closed"); ?>


<h1> User </h1>

<h2> Repositories </h2>

<?php

$res = $api->getUserRepositories("outoftime");

foreach($res as $repo){
    echo $repo[0];
?>
<br>
    <?php
}

?>

<h2> Info </h2>

<?php
    $res =  $api->getUserInfo("mnapoli");

    foreach ($res as $info){
      echo $info;
?>
<br>
        <?php
    }

?>

<h2> Commits </h2>

<?php
 echo $api->getUserCommits("mnapoli","KnpLabs","php-github-api");
?>

<h2> Pull requests </h2>

Open :<?php echo $api->getUserPullRequests("GrahamCampbell","KnpLabs","php-github-api","open"); ?> <br>
Closed :<?php echo $api->getUserPullRequests("GrahamCampbell","KnpLabs","php-github-api","closed"); ?>

</body>
</html>