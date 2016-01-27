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

require_once __DIR__ . "/../src/GithubApi.php";

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
        $res = count($this->_api->getRepositoryCommits("raphaelstarnaud", "testRepo"));

        $this->assertEquals(3, $res);
    }

    /**
     * Test getRepositoryContributors
     * @return void
     */
    public function testGetRepositoryContributors()
    {
        $res = count($this->_api->getRepositoryContributors("raphaelstarnaud", "testRepo"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getRepositoryOpenPullRequests
     * @return void
     */
    public function testGetRepositoryOpenPullRequests()
    {
        $res = count($this->_api->getRepositoryPullRequests("raphaelstarnaud", "testRepo", "open"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getRepositoryClosedPullRequests
     * @return void
     */
    public function testGetRepositoryClosedPullRequests()
    {
        $res = count($this->_api->getRepositoryPullRequests("raphaelstarnaud", "testRepo", "closed"));
        $this->assertEquals(2, $res);
    }

    /**
     * Test getRepositoryOpenIssues
     * @return void
     */
    public function testGetRepositoryOpenIssues()
    {
        $res = count($this->_api->getRepositoryIssues("raphaelstarnaud", "testRepo", "open"));
        $this->assertEquals(2, $res);
    }

    /**
     * Test getRepositoryClosedIssues
     * @return void
     */
    public function testGetRepositoryClosedIssues()
    {
        $res = count($this->_api->getRepositoryIssues("raphaelstarnaud", "testRepo", "closed"));
        $this->assertEquals(2, $res);
    }

    /**
     * Test getUserRepostitories
     * @return void
     */
    public function testGetUserRepostitories()
    {
        $res = count($this->_api->getUserRepositories("outoftime"));
		$this->assertEquals(15, $res);
    }

    /**
     * Test getUserCommits
     * @return void
     */
    public function testGetUserCommits()
    {
        $res = count($this->_api->getUserCommits("RaphaelStArnaud", "raphaelstarnaud", "testRepo"));
        $this->assertEquals(3, $res);
    }

    /**
     * Test getUserOpenPullRequests
     * @return void
     */
    public function testGetUserOpenPullRequests()
    {
        $res = count($this->_api->getUserPullRequests("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "open"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getUserClosedPullRequests
     * @return void
     */
    public function testUserClosedPullRequests()
    {
        $res = count($this->_api->getUserPullRequests("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "closed"));
        $this->assertEquals(2, $res);
    }

    /**
     * Test getUserOpenIssues
     * @return void
     */
    public function testGetUserOpenIssues()
    {
        $res = count($this->_api->getUserIssues("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "open"));
       $this->assertEquals(2, $res);
    }

    /**
     * Test getUserClosedIssue
     * @return void
     */
    public function testGetUserClosedIssues()
    {
        $res = count($this->_api->getUserIssues("RaphaelStArnaud", "raphaelstarnaud", "testRepo", "closed"));
        $this->assertEquals(2, $res);
    }

    /**
     * Test getUserInfo
     * @return void
     */
    public function testGetUserInfo()
    {
        $res = $this->_api->getUserInfo("raphaelstarnaud");
        $this->assertEquals("RaphaelStArnaud", $res['login']);
    }
}
