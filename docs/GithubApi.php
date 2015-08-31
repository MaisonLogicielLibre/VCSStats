<?php
/**
 * Page-Level DocBlock example.
 * @author Raphael St-Arnaud
 */

require_once 'vendor/autoload.php';
session_start();

define("PROJECT_ID","896633240986-agqp6fo71nb7qdbanc3ipc8n83f4opo1@developer.gserviceaccount.com");
define("PROJECT_NAME","nimble-airline-105014");
define("KEY_PATH","key.p12");

/**
 * Allow communication with the GithubApi
 */
class GithubApi
{
    private $client;
    private $googleClient;

    /**
     * Setup
     * Github client : Need to be authenticated with a dummy account to bypass request limit
     * Google client : required project information : https://console.developers.google.com
     */
    public function __construct(){
        $this->client = new \Github\Client();
        $this->client->authenticate("fabulaChildBot", "fabula45", Github\Client::AUTH_HTTP_PASSWORD);

        $this->googleClient = new Google_Client();
        $this->googleClient->setApplicationName("MLL");
        $key = file_get_contents(KEY_PATH);
        $cred = new Google_Auth_AssertionCredentials(
            PROJECT_ID, array(
            'https://www.googleapis.com/auth/bigquery',
            'https://www.googleapis.com/auth/devstorage.full_control'
        ), $key
        );
        $this->googleClient->setAssertionCredentials($cred);
    }

    /**
     * Get the commits of a repository
     * @param1 $owner owner of the repository
     * @param2 $repo name of the repository
     * @return Number of commits
     */
    public function getRepositoryCommits($owner, $repo) {

        return $this->getCommits($owner,$repo);
    }

    /**
     * Get a list of contributors to a repository
     * @param1 $owner owner of the repository
     * @param2 $repo name of the repository
     * @return Array of contributors
     */
    public function getRepositoryContributors($owner, $repo) {

        $res = $this->client->api('repo')->contributors($owner, $repo);

        return $res;
    }

    /**
     * Get the number of pull requests
     * @param1 $owner owner of the repository
     * @param2 $repo name of the repository
     * @param3 $state state of the pullRequest (open,closed,all)
     * @return Number of pull requests
     */
    public function getRepositoryPullRequests($owner, $repo, $state) {

        $res = $this->client->api('pull_request')->all($owner, $repo, array('state' => $state));

        return count($res);
    }

    /**
     * Get the number of issues of a repository
     * @param1 $owner owner of the repository
     * @param2 $repo name of the repository
     * @param3 $state state of the issues (open,closed,all)
     * @return Number of issues
     */
    public function getRepositoryIssues($owner, $repo, $state) {

        $res = $this->client->api('issue')->all($owner, $repo, array('state' => $state));

        return count($res);
    }

    /**
     * Get the information of a user
     * @param1 $user username of the user (login)
     * @return Array of information
     */
    public function getUserInfo($user) {

        $infos =  $this->client->api('user')->show($user);

        $userInfos = [
            "name" => $infos["name"],
            "login" => $infos["login"],
            "location" => $infos["location"]
        ];

        return $userInfos;
    }

    /**
     * Get the number of commits made by a user of a repository
     * @param1 $user the username of the user (login)
     * @param2 $owner owner of the repository
     * @param3 $repo name of the repository
     * @return Number of commits
     */
    public function getUserCommits($user, $owner, $repo) {

        return $this->getCommits($owner,$repo,$user);
    }

    /**
     * Get the number of pull requests made by a user of a repository
     * @param1 $user the username of the user (login)
     * @param2 $owner owner of the repository
     * @param3 $repo name of the repository
     * @param4 $state state of the pullRequest (open,closed,all)
     * @return Number of pull requests
     */
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

    /**
     * Access Github Archive through BigQuery to find out all the repositories
     * that a user contributed to. Unavailable through Github API
     * @param1 $user the username of the user (login)
     * @return Array of repositories
     */
    public function getUserRepositories($user) {

        $service = new Google_Service_Bigquery($this->googleClient);
        $query = new Google_Service_Bigquery_QueryRequest();
        $query->setQuery("SELECT repository_url FROM [githubarchive:github.timeline] WHERE payload_pull_request_user_login ='$user' GROUP BY repository_url");

        $jobs = $service->jobs;
        $job = $jobs->query(PROJECT_NAME, $query);

        $ref = $job->getJobReference();
        $jobId = $ref['jobId'];

        $res = $jobs->getQueryResults(PROJECT_NAME, $jobId, array('timeoutMs' => 1000));

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


    /**
     * Get the number of commits in a repository (all branches).
     * It is possible to filter for a specific user
     * @param1 $owner owner of the repository
     * @param2 $repo name of the repository
     * @param3 $user the username of the user (login) (optional)
     * @return Number of commits
     */
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

    /**
     * Return the branches of a repository
     * @param1 $owner owner of the repository
     * @param2 $repo name of the repository
     * @return Array of branches
     */
    private function getBranches($owner,$repo){
        return $this->client->api('repo')->branches($owner, $repo);
    }

}

?>