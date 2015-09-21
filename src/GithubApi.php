<?php
/**
 * Allow communication with Github API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 *
 * Github API: https://developer.github.com/v3/
 * Github php API : https://github.com/KnpLabs/php-github-api/tree/master/doc
 */

$path= realpath(__DIR__ . '/..');
require_once $path . '/vendor/autoload.php';

define("PROJECT_ID", "481460910115-q5ddd65u4d6hi74fkt1birhl9369scps@developer.gserviceaccount.com");
define("PROJECT_NAME", "maison-1048");
define("KEY_PATH", __DIR__ . "/config/key.p12");

/**
 * Allow communication with Github API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 */
class GithubApi
{
    private $_client;
    private $_googleClient;

    /**
     * Setup
     * Github client : Need to be authenticated with a dummy account to bypass request limit
     * Google client : required project information : https://console.developers.google.com
     * Dummy account : Required to bypass low request limit of Github
     */
    public function __construct()
    {
        $this->_client = new \Github\Client();
        $this->_client->authenticate("fabulaChildBot", "fabula45", Github\Client::AUTH_HTTP_PASSWORD);

        $this->_googleClient = new Google_Client();
        $this->_googleClient->setApplicationName("Maison");
        $key = file_get_contents(KEY_PATH);
        $cred = new Google_Auth_AssertionCredentials(PROJECT_ID, ['https://www.googleapis.com/auth/bigquery', 'https://www.googleapis.com/auth/devstorage.full_control'], $key);
        $this->_googleClient->setAssertionCredentials($cred);
    }

    /**
     * Get the commits of a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Number of commits
     */
    public function getRepositoryCommits($owner, $repo)
    {
        return $this->_getCommits($owner, $repo);
    }

    /**
     * Get a list of contributors to a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return array of contributors
     */
    public function getRepositoryContributors($owner, $repo)
    {
        $res = $this->_client->api('repo')->contributors($owner, $repo);

        return $res;
    }

    /**
     * Get the number of pull requests
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed)
     * @return Number of pull requests
     */
    public function getRepositoryPullRequests($owner, $repo, $state)
    {
        $res = $this->_client->api('pull_request')->all($owner, $repo, ['state' => $state]);

        return count($res);
    }

    /**
     * Get the number of issues of a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed)
     * @return Number of issues
     */
    public function getRepositoryIssues($owner, $repo, $state)
    {
        $res = Count($this->_client->api('issue')->all($owner, $repo, ['state' => $state]));
        $res = $res - $this->getRepositoryPullRequests($owner, $repo, $state);

        return $res;
    }

    /**
     * Get the information of a user
     * @param  string $user username of the user (login)
     * @return array of information
     */
    public function getUserInfo($user)
    {
        $infos = $this->_client->api('user')->show($user);

        $userInfos = [
            "name" => $infos["name"],
            "login" => $infos["login"],
            "email" => $infos["email"]
        ];

        return $userInfos;
    }

    /**
     * Get the number of commits made by a user of a repository
     * @param  string $user  username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Number of commits
     */
    public function getUserCommits($user, $owner, $repo)
    {
        return $this->_getCommits($owner, $repo, $user);
    }

    /**
     * Get the number of pull requests made by a user of a repository
     * @param  string $user  username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed)
     * @return Number of pull requests
     */
    public function getUserPullRequests($user, $owner, $repo, $state)
    {
        $nbPull = 0;

        $pulls = $this->_client->api('pull_request')->all($owner, $repo, ['state' => $state]);

        foreach ($pulls as $pull) {
            $userInfo = $pull['user'];

            if ($userInfo['login'] == $user) {
                $nbPull++;
            }
        }

        return $nbPull;
    }

    /**
     * Access Github Archive through BigQuery to find out all the repositories
     * that a user contributed to.
     * Unavailable through Github API
     * @param  string $user the username of the user (login)
     * @return array of repositories
     */
    public function getUserRepositories($user)
    {
        $service = new Google_Service_Bigquery($this->_googleClient);
        $query = new Google_Service_Bigquery_QueryRequest();
        $query->setQuery("SELECT repository_url FROM [githubarchive:github.timeline] WHERE payload_pull_request_user_login ='$user' GROUP BY repository_url");

        $jobs = $service->jobs;
        $job = $jobs->query(PROJECT_NAME, $query);

        $ref = $job->getJobReference();
        $jobId = $ref['jobId'];

        $res = $jobs->getQueryResults(PROJECT_NAME, $jobId, ['timeoutMs' => 1000]);

        $rows = $res->getRows();
        $repos = [];
        foreach ($rows as $r) {
            $r = $r->getF();
            $temp = [];
            foreach ($r as $v) {
                $temp[] = $v->v;
            }
            $repos[] = $temp;
        }

        return $repos;
    }

    /**
     * Get the number of issues assigned to the user
     * @param  string $user  username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed)
     * @return Number of issues
     */
    public function getUserIssues($user, $owner, $repo, $state)
    {
        $res = count($this->_client->api('issue')->all($owner, $repo, ['state' => $state, 'assigned' => $user]));

        $res = $res - $this->getUserPullRequests($user, $owner, $repo, $state);

        return $res;
    }


    /**
     * Get the number of commits in a repository (all branches).
     * It is possible to filter for a specific user
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $user  username of the user (login)
     * @return Number of commits
     */
    private function _getCommits($owner, $repo, $user = null)
    {
        $commits = 0;

        $branches = $this->_getBranches($owner, $repo);

        foreach ($branches as $branch) {
            $branch = $branch['commit'];

            if ($user === null) {
                $commits += count($this->_client->api('repo')->commits()->all($owner, $repo, ['sha' => $branch['sha']]));
            } else {
                $commits += count($this->_client->api('repo')->commits()->all($owner, $repo, ['sha' => $branch['sha'], 'author' => $user]));
            }
        }

        return $commits;
    }

    /**
     * Return the branches of a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return array of branches
     */
    private function _getBranches($owner, $repo)
    {
        return $this->_client->api('repo')->branches($owner, $repo);
    }
}
