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

$path= realpath(__DIR__ . '/..' . '/..' . '/..');
require_once $path . '/autoload.php';

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
		$commits = [];
		$done = false;
		$curPage = 1;
		
		while(!$done) {
			$curCommits = $this->_client->api('repo')->commits()->all($owner, $repo, ['per_page' => 100, 'page' => $curPage]);
			
			$commits = array_merge($commits, $curCommits);
			
			if(count($curCommits) < 100) {
				$done = true;
			}
			
			$curPage++;
		}
		
        return $commits;
    }

    /**
     * Get a list of contributors to a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return array of contributors
     */
    public function getRepositoryContributors($owner, $repo)
    {
		$contributors = [];
		$done = false;
		$curPage = 1;
		
		while(!$done) {
			$curContributors = $this->_client->api('repo')->contributors($owner, $repo, ['per_page' => 100, 'page' => $curPage]);
			
			$contributors = array_merge($contributors, $curContributors);
			
			if(count($curContributors) < 100) {
				$done = true;
			}
			
			$curPage++;
		}
        
        return $contributors;
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
		$prs = [];
		$done = false;
		$curPage = 1;
		
		while(!$done) {
			$curPr = $this->_client->api('pull_request')->all($owner, $repo, ['state' => $state, 'per_page' => 100, 'page' => $curPage]);
			
			$prs = array_merge($prs, $curPr);
			
			if(count($curPr) < 100) {
				$done = true;
			}
			
			$curPage++;
		}
        

        return $prs;
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
		$issues = [];
		$done = false;
		$curPage = 1;
		
		while(!$done) {
			$curIssues = $this->_client->api('issue')->all($owner, $repo, ['state' => $state, 'per_page' => 100, 'page' => $curPage]);
			
			foreach($curIssues as $issue) {
				if (!array_key_exists('pull_request', $issue)) {
					$issues[] = $issue;
				}
			}
			
			if(count($curIssues) < 100) {
				$done = true;
			}
			
			$curPage++;
		}
	
        return $issues;
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
        $done = false;
		$curPage = 1;
		$commits = [];
		
        while (!$done) {

            $userCommits = $this->_client->api('repo')->commits()->all($owner, $repo, ['per_page' => 100, 'page' => $curPage, 'author' => $user]);
         
			if(count($userCommits) < 100) {
				$done = true;
			}
			
			$curPage++;
			
			$commits = array_merge($commits, $userCommits);
        }

        return $commits;
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
		$done = false;
		$curPage = 1;
		$prs = [];
		
		while(!$done)  {
			$curPrs = $this->_client->api('pull_request')->all($owner, $repo, ['per_page' => 100, 'page' => $curPage, 'state' => $state]);

			foreach ($curPrs as $pr) {
				$userInfo = $pr['user'];

				if ($userInfo['login'] == $user) {
					$prs[] = $pr;
				}
			}
			
			if(count($curPrs) < 100) {
				$done = true;
			}
			
			$curPage++;
		}
		
        return $prs;
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
		$issues = [];
		$done = false;
		$curPage = 1;
		
		while(!$done) {
			$curIssues = $this->_client->api('issue')->all($owner, $repo, ['state' => $state, 'per_page' => 100, 'page' => $curPage]);
			
			foreach($curIssues as $issue) {
				if (($issue['user']['login'] == $user) && (!array_key_exists('pull_request', $issue))) {
					$issues[] = $issue;
				}
			}
			
			if(count($curIssues) < 100) {
				$done = true;
			}
			
			$curPage++;
		}
	
        return $issues;
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
