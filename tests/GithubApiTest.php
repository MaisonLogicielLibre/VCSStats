<?php
/**
 * Test for Github API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 */

require_once "GithubApi.php";

/**
 * Test for Github API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 */
class GithubApiTest extends PHPUnit_Framework_TestCase
{
    private $_api;

    /**
     * Instanciate the API
     * @return void
     */
    protected function setUp()
    {
        $this->_api = new GithubApi;
    }

    /**
     * Test getRepositoryCommits
     * @return void
     */
    public function testGetRepositoryCommits()
    {
        $res = $this->_api->getRepositoryCommits("raphaelstarnaud", "testRepo");
        $this->assertTrue($res == 7);
    }

    /**
     * Test getRepositoryContributors
     * @return void
     */
    public function testGetRepositoryContributors()
    {
        $res = count($this->_api->getRepositoryContributors("raphaelstarnaud", "testRepo"));
        $this->assertTrue($res == 1);
    }

    /**
     * Test getRepositoryOpenPullRequests
     * @return void
     */
    public function testGetRepositoryOpenPullRequests()
    {
        $res = $this->_api->getRepositoryPullRequests("raphaelstarnaud", "testRepo", "open");
        $this->assertTrue($res == 1);
    }

    /**
     * Test getRepositoryClosedPullRequests
     * @return void
     */
    public function testGetRepositoryClosedPullRequests()
    {
        $res = $this->_api->getRepositoryPullRequests("raphaelstarnaud", "testRepo", "closed");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getRepositoryOpenIssues
     * @return void
     */
    public function testGetRepositoryOpenIssues()
    {
        $res = $this->_api->getRepositoryIssues("raphaelstarnaud", "testRepo", "open");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getRepositoryClosedIssues
     * @return void
     */
    public function testGetRepositoryClosedIssues()
    {
        $res = $this->_api->getRepositoryIssues("raphaelstarnaud", "testRepo", "closed");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getUserRepostitories
     * @return void
     */
    public function testGetUserRepostitories()
    {
        $res = $this->_api->getUserRepositories("outoftime");
        $this->assertTrue(count($res) == 15);
    }

    /**
     * Test getUserCommits
     * @return void
     */
    public function testGetUserCommits()
    {
        $res = $this->_api->getUserCommits("RaphaelStArnaud", "raphaelstarnaud", "testRepo");
        $this->assertTrue($res == 7);
    }

    /**
     * Test getUserOpenPullRequests
     * @return void
     */
    public function testGetUserOpenPullRequests()
    {
        $res = $this->_api->getUserPullRequests("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "open");
        $this->assertTrue($res == 1);
    }

    /**
     * Test getUserClosedPullRequests
     * @return void
     */
    public function testUserClosedPullRequests()
    {
        $res = $this->_api->getUserPullRequests("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "closed");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getUserOpenIssues
     * @return void
     */
    public function testGetUserOpenIssues()
    {
        $res = $this->_api->getUserIssues("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "open");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getUserClosedIssue
     * @return void
     */
    public function testGetUserClosedIssues()
    {
        $res = $this->_api->getUserIssues("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "closed");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getUserInfo
     * @return void
     */
    public function testGetUserInfo()
    {
        $res = $this->_api->getUserInfo("raphaelstarnaud");
        $this->assertTrue(count($res) == 3);
    }
}
