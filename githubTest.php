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
    echo $api->getRepositoryCommits("raphaelstarnaud","testRepo");;
?>

<h2> Contributors </h2>

<?php
    $contributors = $api->getRepositoryContributors("raphaelstarnaud","testRepo");
    foreach($contributors as $contributor){
        echo $contributor['login'];
        ?>
        <br>
        <?php
    }

?>

<h2> Pull requests</h2>

Open :<?php echo $api->getRepositoryPullRequests("raphaelstarnaud","testRepo","open"); ?> <br>
Closed :<?php echo $api->getRepositoryPullRequests("raphaelstarnaud","testRepo","closed"); ?>

<h2> Issues</h2>

Open :<?php echo $api->getRepositoryIssues("raphaelstarnaud","testRepo","open"); ?> <br>
Closed :<?php echo $api->getRepositoryIssues("raphaelstarnaud","testRepo","closed"); ?>


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
    $res =  $api->getUserInfo("RaphaelStArnaud");

    foreach ($res as $info){
      echo $info;
?>
<br>
        <?php
    }

?>

<h2> Commits </h2>

<?php
 echo $api->getUserCommits("RaphaelStArnaud","raphaelstarnaud","testRepo");;
?>

<h2> Pull requests </h2>

Open :<?php echo $api->getUserPullRequests("RaphaelStArnaud","raphaelstarnaud","testRepo","open"); ?> <br>
Closed :<?php echo $api->getUserPullRequests("RaphaelStArnaud","raphaelstarnaud","testRepo","closed"); ?>

<h2> Issues</h2>

Open :<?php echo $api->getUserIssues("RaphaelStArnaud","raphaelstarnaud","testRepo","open"); ?> <br>
Closed :<?php echo $api->getUserIssues("RaphaelStArnaud","raphaelstarnaud","testRepo","closed"); ?>

</body>
</html>