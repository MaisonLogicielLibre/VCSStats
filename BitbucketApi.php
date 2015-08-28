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

        $res = $this->getCommits($owner,$repo);

        return count($res);
    }

    public function getRepositoryContributors($owner, $repo) {
        $contributors = array();

        $commits = $this->getCommits($owner,$repo);

        foreach($commits as $commit){
            $author = $commit['author'];
            $userInfo = $author['user'];

            if(!in_array($userInfo['username'],$contributors))
                array_push($contributors,$userInfo['username']);
        }

        return $contributors;
    }

    public function getRepositoryPullRequests($owner, $repo, $state) {

        $count = 0;

        if($state == 'open'){
            $pulls= $this->getPullRequests($owner,$repo,'OPEN');
            $count += count($pulls);

        }
        else{
            $pulls = $this->getPullRequests($owner,$repo,'MERGED');
            $count += count($pulls);

            $pulls = $this->getPullRequests($owner,$repo,'DECLINED');
            $count += count($pulls);
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

        $users = new Bitbucket\API\Users();

        $userInfo = $users->get($user);

        $infos = json_decode($userInfo->getContent(),true);

        $res = [
            'login' => $infos["username"],
            'name' => $infos["display_name"],
            'location' => $infos["location"],
        ];

        return $res;
    }

    public function getUserCommits($user) {
        $nbCommits = 0;
        $repos = $this->getUserRepositories($user);

        error_reporting(0);

        foreach($repos as $repo){
            $link = explode('/',$repo);
            $owner = $link[count($link)-2];
            $repo = $link[count($link)-1];

            $commits = $this->getCommits($owner,$repo);

            foreach($commits as $commit){
                $author = $commit['author'];
                $userInfo = $author['user'];

                if($userInfo['username'] == $user);
                    $nbCommits += 1;
            }

        }

        error_reporting(-1);

        return $nbCommits;
    }

    public function getUserPullRequests($user,$state) {
        $count=0;
        $repos = $this->getRepositoriesUser($user);

        error_reporting(0);

        foreach($repos as $repo){
            $link = explode('/',$repo);
            $owner = $link[count($link)-2];
            $repo = $link[count($link)-1];

            if($state == 'open'){
                $pulls = $this->getPullRequests($owner,$repo,'OPEN');
                $count += count($pulls);
            }
            else{
                $pulls = $this->getPullRequests($owner,$repo,'MERGED');
                $count += count($pulls);

                $pulls = $this->getPullRequests($owner,$repo,'DECLINED');
                $count += count($pulls);
            }

        }

        error_reporting(-1);

        return $count;
    }

    public function getUserRepositories($user) {
        $repos = $this->getRepositoriesUser($user);

        $reps =array();

        foreach($repos as $repo){
            $info = $repo['links'];
            $info = $info['html'];
            array_push($reps,$info['href']);
        }

        return $reps;
    }

    private function getCommits($owner,$repo){

        $commits = new Bitbucket\API\Repositories\Commits();
        $response = $commits->all($owner, $repo);

        $res = json_decode($response->getContent(), true);

        return $res['values'];
    }

    private function getPullRequests($owner,$repo,$state){
        $pull = new Bitbucket\API\Repositories\PullRequests();

        $response = $pull->all($owner, $repo, array('state' => $state));
        $res = json_decode($response->getContent(), true);

        return $res;
    }

    private function getRepositoriesUser($user){
        $repos =  new Bitbucket\API\Repositories();
        $repoInfos =   $repos->all($user);

        $res = json_decode($repoInfos->getContent(),true);

        return $res['values'];
    }
}

?>