<?php

require_once 'vendor/autoload.php';
session_start();

class GithubApi
{
    private $client;
    private $googleClient;

    public function __construct(){
        $this->client = new \Github\Client();
        $this->client->authenticate("fabulaChildBot", "fabula45", Github\Client::AUTH_HTTP_PASSWORD);

        $this->googleClient = new Google_Client();
        $this->googleClient->setApplicationName("MLL");
        //$service_token = @file_get_contents('token.json');
        //$this->googleClient->setAccessToken($service_token);
        $key = file_get_contents('key.p12');
        $cred = new Google_Auth_AssertionCredentials(
            '896633240986-agqp6fo71nb7qdbanc3ipc8n83f4opo1@developer.gserviceaccount.com', array(
            'https://www.googleapis.com/auth/bigquery',
            'https://www.googleapis.com/auth/devstorage.full_control'
        ), $key
        );
        $this->googleClient->setAssertionCredentials($cred);
        //$this->googleClient->setAccessToken($this->googleClient->getAccessToken());

    }

    public function getRepositoryCommits($owner, $repo) {

        return $this->getCommits($owner,$repo);
    }

    public function getRepositoryContributors($owner, $repo) {

        $res = $this->client->api('repo')->contributors($owner, $repo);

        return $res;
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

        $service = new Google_Service_Bigquery($this->googleClient);
        $query = new Google_Service_Bigquery_QueryRequest();
        $query->setQuery("SELECT repository_url FROM [githubarchive:github.timeline] WHERE payload_pull_request_user_login ='$user' GROUP BY repository_url");

        $jobs = $service->jobs;
        $job = $jobs->query('nimble-airline-105014', $query);

        $ref = $job->getJobReference();
        $jobId = $ref['jobId'];

        $res = $jobs->getQueryResults('nimble-airline-105014', $jobId, array('timeoutMs' => 1000));

        $rows = $res->getRows();
        $repos = array();
        foreach ($rows as $r) {
            $r = $r->getF();
            $temp = array();
            foreach ($r as $v) {
                $temp[] = $v->v;
            }
            $repos[] = $temp;
        }

        return $repos;

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