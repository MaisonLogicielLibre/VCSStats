<?php



require_once("BitbucketApi.php");
$api= new BitbucketApi;
?>

<!DOCTYPE html>
<html>
<body>

<h1> Repository</h1>

<h2> Commits </h2>

<?php
    echo $api->getRepositoryCommits("jpcomeau","orion_equipe");
?>

<h2> Contributors </h2>

<?php
$contributors = $api->getRepositoryContributors("jpcomeau","orion_equipe");

foreach($contributors as $contributor){
    echo $contributor;
    ?>
    <br>
    <?php
}
?>

<h2> Pull requests</h2>

Open :<?php echo $api->getRepositoryPullRequests("rstarnaud","testrepo","open"); ?> <br>
Closed :<?php echo $api->getRepositoryPullRequests("rstarnaud","testrepo","closed"); ?>

<h2> Issues</h2>

New :<?php echo $api->getRepositoryIssues("rstarnaud","testrepo","new"); ?> <br>
Closed :<?php echo $api->getRepositoryIssues("rstarnaud","testrepo","closed"); ?>


<h1> User </h1>

<h2> Repositories </h2>

<?php

$repos = $api->getUserRepositories("rstarnaud");

foreach($repos as $repo){
    echo $repo;
    ?>
    <br>
    <?php
}
?>

<h2> Info </h2>

<?php
    $userInfo = $api->getUserInfo("rstarnaud");

    foreach($userInfo as $info){
        echo $info;
?>
        <br>
        <?php
    }

?>

<h2> Commits </h2>

<?php
 echo $api->getUserCommits("rstarnaud","rstarnaud","testrepo");
?>

<h2> Pull requests </h2>

Open :<?php  echo $api->getUserPullRequests("rstarnaud","rstarnaud","testrepo","open");  ?> <br>
Closed :<?php echo $api->getUserPullRequests("rstarnaud","rstarnaud","testrepo","closed") ?>

<h2> Issues</h2>

New :<?php echo $api->getUserIssues("rstarnaud","rstarnaud","testrepo","new"); ?> <br>
Closed :<?php echo $api->getUserIssues("rstarnaud","rstarnaud","testrepo","closed"); ?>

</body>
</html>