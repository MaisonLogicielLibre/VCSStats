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

require_once 'vendor/autoload.php';

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
        $this->_client = new \Bitbucket\API\Api();
        $this->_client->getClient()->addListener(new \Bitbucket\API\Http\Listener\BasicAuthListener('fabulaChildBot', 'solarus45'));

    }

    /**
     * Get the number of commits of the repo
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Number of commits
     */
    public function getRepositoryCommits($owner, $repo)
    {

        $res = $this->_getCommits($owner, $repo);

        return count($res);
    }

    /**
     * Get a list of contributors to a repository
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Array of contributors
     */
    public function getRepositoryContributors($owner, $repo)
    {
        $contributors = array();

        $commits = $this->_getCommits($owner, $repo);

        foreach ($commits as $commit) {
            $author = $commit['author'];
            $userInfo = $author['user'];

            if(!in_array($userInfo['username'], $contributors))
                array_push($contributors, $userInfo['username']);
        }

        return $contributors;
    }

    /**
     * Get the number of pull requests (Only for the default branch)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed)
     * @return Number of pull requests
     */
    public function getRepositoryPullRequests($owner, $repo, $state)
    {
        $count = 0;

        if ($state == 'open') {
            $pulls= $this->_getPullRequests($owner, $repo, 'OPEN');

            $count += count($pulls);
        } else {
            $pulls= $this->_getPullRequests($owner, $repo, 'DECLINED');

            $count += count($pulls);
        }

        return $count;
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

        $issue = new Bitbucket\API\Repositories\Issues();
        $response = $issue->all($owner, $repo, array('status' => $state));
        $res = json_decode($response->getContent(), true);

        return $res['count'];
    }

    /**
     * Get the information of a user
     * @param  string $user username of the user (login)
     * @return Array of information
     */
    public function getUserInfo($user)
    {

        $users = new Bitbucket\API\Users();

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
        $nbCommits = 0;

        $commits = $this->_getCommits($owner, $repo);

        error_reporting(0);

        foreach ($commits as $commit) {
            $author = $commit['author'];
            $userInfo = $author['user'];

            if($userInfo['username'] == $user);
                $nbCommits += 1;
        }

        error_reporting(-1);

        return $nbCommits;
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

        if ($state == 'open') {
            $pulls = $this->_getPullRequests($owner, $repo, 'OPEN');
            $count = $this->_filterPullRequests($user, $pulls);
        } else {
            $pulls = $this->_getPullRequests($owner, $repo, 'DECLINED');
            $count = $this->_filterPullRequests($user, $pulls);
        }

        return $count;
    }

    /**
     * Access the number of repositories owned by the user
     * (No way of getting all repos he contributed to)
     * @param  string $user the username of the user (login)
     * @return Array of repositories
     */
    public function getUserRepositories($user)
    {

        $rep =  new Bitbucket\API\Repositories();
        $repoInfos =   $rep->all($user);

        $repos = json_decode($repoInfos->getContent(), true);

        $reps = array();

        foreach ($repos['values'] as $repo) {
            $info = $repo['links'];
            $info = $info['html'];
            array_push($reps, $info['href']);
        }

        return $reps;
    }

    /**
     * Get the number of issues assigned to a user
     * @param  string $user  the username of the user (login)
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pullRequest (open,closed,all)
     * @return Number of issues
     */
    public function getUserIssues($user, $owner, $repo, $state)
    {
        $issue = new Bitbucket\API\Repositories\Issues();
        $response = $issue->all($owner, $repo, array('status' => $state, 'repsonsible' => $user));
        $res = json_decode($response->getContent(), true);

        return $res['count'];
    }

    /**
     * Get the commits in a repository (all branches).
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @return Array of commits
     */
    private function _getCommits($owner, $repo)
    {

        $commits = new Bitbucket\API\Repositories\Commits();
        $response = $commits->all($owner, $repo);

        $res = json_decode($response->getContent(), true);

        return $res['values'];
    }

    /**
     * Get the number of pull requests in a repository.
     * @param  string $owner owner of the repository
     * @param  string $repo  name of the repository
     * @param  string $state state of the pull requests (OPEN,CLOSED)
     * @return Array of pull requests
     */
    private function _getPullRequests($owner, $repo, $state)
    {
        $pull = new Bitbucket\API\Repositories\PullRequests();

        $response = $pull->all($owner, $repo, array('state' => $state));

        $res = json_decode($response->getContent(), true);

        return $res['values'];
    }

    /**
     * Filter the pull requests for a user
     * @param  string $user  the username of the user (login)
     * @param  string $pulls the pull requests
     * @return Number of filtered pull requests
     */
    private function _filterPullRequests($user, $pulls)
    {
        $count = 0;
        foreach ($pulls as $pull) {
            $author = $pull['author'];
            $username = $author['username'];

            if($username == $user)
                $count += 1;

        }

        return $count;
    }
}

?>