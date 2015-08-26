<?php

require_once 'vendor/autoload.php';

class BitbucketApi
{
    private $client;

    public function __construct(){
        $this->client = new \Bitbucket\API\Api();
        $this->client->getClient()->addListener(new \Bitbucket\API\Http\Listener\BasicAuthListener('fabulaChildBot', 'solarus45'));
    }

    public function getRepositoryCommits($owner, $repo) {

        $commits = new Bitbucket\API\Repositories\PullRequests();
        $response = $commits->all($owner, $repo);

        $res = json_decode($response->getContent(), true);

        return count($res['values']);
    }

    public function getRepositoryContributors($owner, $repo) {

        //Pending
    }

    public function getRepositoryPullRequests($owner, $repo, $state) {
        $pull = new Bitbucket\API\Repositories\PullRequests();
        $count = 0;

        if($state == 'open'){
            $response = $pull->all($owner, $repo, array('state' => 'OPEN'));
            $res = json_decode($response->getContent(), true);
            $count += count($res);

        }
        else{
            $response = $pull->all($owner, $repo, array('state' => 'MERGED'));
            $res = json_decode($response->getContent(), true);
            $count += count($res);

            $response = $pull->all($owner, $repo, array('state' => 'DECLINED'));
            $res = json_decode($response->getContent(), true);
            $count += count($res);
        }

        return $count;
    }

    public function getRepositoryIssues($owner, $repo, $state) {

        $issue = new Bitbucket\API\Repositories\Issues();
        $response = $issue->all($owner, $repo, array('status' => $state));
        $res = json_decode($response->getContent(),true);

        return $res['count'];
    }

    public function getUserInfo($user) {

        $userInfo = new Bitbucket\API\User();
        $userInfo->setCredentials( new Bitbucket\API\Authentication\Basic($user,'doesnotmatter') );
        $profil = $userInfo->get();
        $res = $profil->getContent();

        return $res;
    }

    public function getUserCommits($user) {

        //Pending
    }

    public function getUserPullRequests($user) {
        //Pending
    }

    public function getUserRepositories($user) {

        //Pending
    }
}

?>