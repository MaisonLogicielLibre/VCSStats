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
    echo $api->getRepositoryCommits("MaisonLogicielLibre","TableauDeBord");
?>

<h2> Contributors </h2>

<?php
echo $api->getRepositoryContributors("MaisonLogicielLibre","TableauDeBord");
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

$res = $api->getUserRepositories("rakku45");
foreach($res AS $repo){
    echo $repo["name"];
    echo "\n";
}

?>

<h2> Info </h2>

<?php
    $res =  $api->getUserInfo("rakku45");

    foreach ($res as $info){
      echo $info;
      echo "\n";
    }

?>

<h2> Commits </h2>

<?php
 echo $api->getUserCommits("ornicar");
?>

<h2> Pull requests </h2>

<?php $res = $api->getUserPullRequests("ornicar"); ?>

Open :<?php echo $res["open"] ?> <br>
Closed :<?php echo $res["closed"] ?>

</body>
</html>