<?php
/**
 * Allow communication with Bitbucket API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 *
 * Bitbucket API : https://confluence.atlassian.com/bitbucket/use-the-bitbucket-rest-apis-222724129.html
 * Bitbucket browser API : http://restbrowser.bitbucket.org/
 * Bitbucket php API : http://gentlero.bitbucket.org/bitbucket-api/
 */

namespace VCSStats;

use Bitbucket\API\Api;
use Bitbucket\API\Http\Listener\BasicAuthListener;
use Bitbucket\API\Repositories\Commits;
use Bitbucket\API\Repositories\PullRequests;
use Bitbucket\API\Repositories\Issues;
use Bitbucket\API\Repositories;
use Bitbucket\API\Users;

/**
 * Allow communication with Bitbucket API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 */
class BitbucketApi
{
    private $_client;

    /**
     * Allow communication with the Bitbucket Api
     * Dummy account : Required to bypass low request limit of Bitbucket
     */
    public function __construct()
    {
        $this->_client = new Api();
        $this->_client->getClient()->addListener(new BasicAuthListener('fabulaChildBot', 'solarus45'));
    }

    /**
     * Get the number of commits of the repo
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Number of commits
     */
    public function getRepositoryCommits($owner, $repo)
    {
        $commits = [];
		$page = 1;
		$done = false;

        $commit = new Commits();

		while(!$done) {
			$response = $commit->all($owner, $repo, ['page' => $page, 'pagelen' => 50]);
			$res = json_decode($response->getContent(), true);

			$commits = array_merge($commits, $res['values']);

			if (count($res['values']) < 50) {
				$done = true;
			}

			$page++;
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

        $commits = $this->getRepositoryCommits($owner, $repo);

        foreach ($commits as $commit) {
            $author = $commit['author'];
            $userInfo = $author['user'];

            if (!in_array($userInfo['username'], $contributors)) {
                    array_push($contributors, $userInfo['username']);
            }
        }

        return $contributors;
    }

    /**
     * Get the number of pull requests (Only for the default branch)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (OPEN,MERGED,DECLINED)
     * @return Number of pull requests
     */
    public function getRepositoryPullRequests($owner, $repo, $state)
    {
		$prs = [];
		$page = 1;
		$done = false;

        $pull = new PullRequests();

		while(!$done) {
			$response = $pull->all($owner, $repo, ['state' => $state, 'page' => $page, 'pagelen' => 50]);
			$res = json_decode($response->getContent(), true);

			$prs = array_merge($prs, $res['values']);

			if (count($res['values']) < 50) {
				$done = true;
			}

			$page++;
		}

        return $prs;

    }

    /**
     * Get the number of issues of a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,new,resolved,on hold,invalid,duplicate,wontfix)
     * @return Number of issues
     */
    public function getRepositoryIssues($owner, $repo, $state)
    {
		$issues = [];
		$start = 0;
		$done = false;

        $issue = new Issues();

		while(!$done) {
			$response = $issue->all($owner, $repo, ['status' => $state, 'start' => $start, 'limit' => 50]);
			$res = json_decode($response->getContent(), true);

			$issues = array_merge($issues, $res['issues']);

			if (count($res['issues']) < 50) {
				$done = true;
			}

			$start = $start+50;
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
        $users = new Users();

        $userInfo = $users->get($user);

        $infos = json_decode($userInfo->getContent(), true);

        $res = [
            'login' => $infos["username"],
            'name' => $infos["display_name"]
        ];

        return $res;
    }

    /**
     * Get the number of commits made by a user of a repository
     * @param  string $user  the username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Number of commits
     */
    public function getUserCommits($user, $owner, $repo)
    {
        $commits = $this->getRepositoryCommits($owner, $repo);
		$userCommits = [];

        foreach ($commits as $commit) {
            $author = $commit['author'];

			if(array_key_exists('user', $author)) {
				$userInfo = $author['user'];

				if ($userInfo['username'] == $user) {
					$userCommits[]= $commit;
				}
			}
        }

        return $userCommits;
    }

    /**
     * Get the number of pull requests made by a user of a repository
     * @param  string $user  the username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed,all)
     * @return Number of pull requests
     */
    public function getUserPullRequests($user, $owner, $repo, $state)
    {
		$userPrs = [];

		$prs = $this->getRepositoryPullRequests($owner, $repo, $state);

		foreach ($prs as $pr) {
            $author = $pr['author'];
            $username = $author['username'];

            if ($username == $user) {
                $userPrs[] = $pr;
            }
        }

        return $userPrs;
    }

    /**
     * Access the number of repositories owned by the user
     * (No way of getting all repos he contributed to)
     * @param  string $user the username of the user (login)
     * @return array of repositories
     */
    public function getUserRepositories($user)
    {
		$reps = [];
		$page = 1;
		$done = false;

        $rep = new Repositories();

		while(!$done) {
			$response = $rep->all($user, ['page' => $page, 'pagelen' => 50]);
			$res = json_decode($response->getContent(), true);

			$reps = array_merge($reps, $res['values']);

			if (count($res['values']) < 50) {
				$done = true;
			}

			$page++;
		}

        return $reps;
    }

    /**
     * Get the number of issues assigned to a user
     * @param  string $user  the username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (OPEN,MERGED,DECLINED)
     * @return Number of issues
     */
    public function getUserIssues($user, $owner, $repo, $state)
    {
		$issues = [];
		$start = 0;
		$done = false;

        $issue = new Issues();

		while(!$done) {
			$response = $issue->all($owner, $repo, ['status' => $state, 'repsonsible' => $user, 'start' => $start, 'limit' => 50]);
			$res = json_decode($response->getContent(), true);

			$issues = array_merge($issues, $res['issues']);

			if (count($res['issues']) < 50) {
				$done = true;
			}

			$start = $start+50;
		}

        return $issues;
    }
}
