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
    echo $api->getRepositoryCommits("controlsfx","controlsfx");
?>

<h2> Contributors </h2>

<?php
$contributors = $api->getRepositoryContributors("controlsfx","controlsfx");

foreach($contributors as $contributor){
    echo $contributor;
    ?>
    <br>
    <?php
}
?>

<h2> Pull requests</h2>

Open :<?php echo $api->getRepositoryPullRequests("controlsfx","controlsfx","open"); ?> <br>
Closed :<?php echo $api->getRepositoryPullRequests("controlsfx","controlsfx","closed"); ?>

<h2> Issues</h2>

Open :<?php echo $api->getRepositoryIssues("controlsfx","controlsfx","invalid"); ?> <br>
Closed :<?php echo $api->getRepositoryIssues("controlsfx","controlsfx","resolved"); ?>


<h1> User </h1>

<h2> Repositories </h2>

<?php

$repos = $api->getUserRepositories("jpcomeau");

foreach($repos as $repo){
    echo $repo;
    ?>
    <br>
    <?php
}
?>

<h2> Info </h2>

<?php
    $userInfo = $api->getUserInfo("jpcomeau");

    foreach($userInfo as $info){
        echo $info;
?>
        <br>
        <?php
    }

?>

<h2> Commits </h2>

<?php
 echo $api->getUserCommits("jpcomeau");
?>

<h2> Pull requests </h2>

Open :<?php  echo $api->getUserPullRequests("jpcomeau","open");  ?> <br>
Closed :<?php echo $api->getUserPullRequests("jpcomeau","closed") ?>

</body>
</html>