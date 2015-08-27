<?php

require_once 'vendor/autoload.php';

class GithubApi
{
    private $client;

    public function __construct(){
        $this->client = new \Github\Client();
        $this->client->authenticate("fabulaChildBot", "fabula45", Github\Client::AUTH_HTTP_PASSWORD);
    }

    public function getRepositoryCommits($owner, $repo) {

        return $this->getCommits($owner,$repo);
    }

    public function getRepositoryContributors($owner, $repo) {

        $res = $this->client->api('repo')->contributors($owner, $repo);
        return count($res);
    }

    public function getRepositoryPullRequests($owner, $repo, $state) {

        $res = $this->client->api('pull_request')->all($owner, $repo, array('state' => $state));

        return count($res);
    }

    public function getRepositoryIssues($owner, $repo, $state) {

        $res = $this->client->api('issue')->all($owner, $repo, array('state' => $state));

        return count($res);
    }

    public function getUserInfo($user) {

        $infos =  $this->client->api('user')->show($user);

        $userInfos = [
            "name" => $infos["name"],
            "login" => $infos["login"],
            "location" => $infos["location"]
        ];

        return $userInfos;
    }

    public function getUserCommits($user, $owner, $repo) {

        return $this->getCommits($owner,$repo,$user);
    }

    public function getUserPullRequests($user, $owner, $repo, $state) {
        $nbPull = 0;

        $pulls = $this->client->api('pull_request')->all($owner, $repo, array('state' => $state));

        foreach($pulls as $pull){
            $userInfo = $pull['user'];

            if($userInfo['login'] == $user)
                $nbPull++;
        }

        return $nbPull;
    }

    public function getUserRepositories($user) {

        //Pending
    }

    private function getCommits($owner, $repo, $user=null){
        $commits = 0;

        $branches = $this->getBranches($owner,$repo);

        foreach($branches AS $branch){
            $branch = $branch['commit'];
            if($user === null){
                $commits +=  count($this->client->api('repo')->commits()->all($owner, $repo, array('sha' => $branch['sha'])));
            }
            else{
                $commits +=  count($this->client->api('repo')->commits()->all($owner, $repo, array('sha' => $branch['sha'],'author' => $user)));
            }
        }

        return $commits;

    }

    private function getBranches($owner,$repo){
        return $this->client->api('repo')->branches($owner, $repo);
    }

}

?>