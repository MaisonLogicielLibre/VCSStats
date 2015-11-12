<?php
/**
 * Test for Bitbucket API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 */

require_once "BitbucketApi.php";

/**
 * Test for Bitbucket API
 *
 * @category API
 * @package  API
 * @author   Raphael St-Arnaud <am21830@ens.etsmtl.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @link     API
 */
class BitbucketApiTest extends PHPUnit_Framework_TestCase
{
    private $_api;

    /**
     * Instanciate the API
     * @return void
     */
    protected function setUp()
    {
        $this->_api = new BitbucketApi;
    }

    /**
     * Test getRepositoryCommits
     * @return void
     */
    public function testGetRepositoryCommits()
    {
        $res = count($this->_api->getRepositoryCommits("jpcomeau", "orion_equipe"));
        $this->assertEquals(389, $res);
    }

    /**
     * Test getRepositoryContributors
     * @return void
     */
    public function testGetRepositoryContributors()
    {
        $res = count($this->_api->getRepositoryContributors("jpcomeau", "orion_equipe"));
        $this->assertEquals(9, $res);
    }

    /**
     * Test getRepositoryPullRequests - OPEN
     * @return void
     */
    public function testGetRepositoryOpenPullRequests()
    {
        $res = count($this->_api->getRepositoryPullRequests("rstarnaud", "testrepo", "OPEN"));
        $this->assertEquals(1, $res);
    }
	
	/**
     * Test getRepositoryPullRequests - MERGED
     * @return void
     */
    public function testGetRepositoryMergedPullRequests()
    {
        $res = count($this->_api->getRepositoryPullRequests("rstarnaud", "testrepo", "MERGED"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getRepositoryPullRequests - DECLINED
     * @return void
     */
    public function testGetRepositoryClosedPullRequests()
    {
        $res = count($this->_api->getRepositoryPullRequests("rstarnaud", "testrepo", "DECLINED"));
        $this->assertEquals(2, $res);
    }

	/**
     * Test getRepositoryIssues - open
     * @return void
     */
    public function testGetRepositoryOpenIssues()
    {
        $res = count($this->_api->getRepositoryIssues("rstarnaud", "testrepo", "open"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getRepositoryIssues - closed
     * @return void
     */
    public function testGetRepositoryClosedIssues()
    {
        $res = count($this->_api->getRepositoryIssues("rstarnaud", "testrepo", "closed"));
        $this->assertEquals(1, $res);;
    }

    /**
     * Test getUserRepostitories
     * @return void
     */
    public function testGetUserRepostitories()
    {
        $res = count($this->_api->getUserRepositories("rstarnaud"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getUserCommits
     * @return void
     */
    public function testGetUserCommits()
    {
        $res = count($this->_api->getUserCommits("rstarnaud", "rstarnaud", "testrepo"));
        $this->assertEquals(4, $res);
    }

    /**
     * Test getUserPullRequests - OPEN
     * @return void
     */
    public function testGetUserOpenPullRequests()
    {
        $res = count($this->_api->getUserPullRequests("rstarnaud", "rstarnaud", "testrepo", "OPEN"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getUserPullRequests - MERGED
     * @return void
     */
    public function testGetUserMergedPullRequests()
    {
        $res = count($this->_api->getUserPullRequests("rstarnaud", "rstarnaud", "testrepo", "MERGED"));
        $this->assertEquals(1, $res);
    }
	
	/**
     * Test getUserPullRequests - DECLINED
     * @return void
     */
    public function testGetUserDeclinedPullRequests()
    {
        $res = count($this->_api->getUserPullRequests("rstarnaud", "rstarnaud", "testrepo", "DECLINED"));
        $this->assertEquals(2, $res);
    }

    /**
     * Test getUserIssues - open
     * @return void
     */
    public function testGetUserOpenIssues()
    {
        $res = count($this->_api->getUserIssues("rstarnaud", "rstarnaud", "testrepo", "open"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getUserIssue - closed
     * @return void
     */
    public function testGetUserClosedIssues()
    {
        $res = count($this->_api->getUserIssues("rstarnaud", "rstarnaud", "testrepo", "closed"));
        $this->assertEquals(1, $res);
    }

    /**
     * Test getUserInfo
     * @return void
     */
    public function testGetUserInfo()
    {
        $res = $this->_api->getUserInfo("rstarnaud");

        $this->assertEquals("rstarnaud", $res['login']);
    }
}
