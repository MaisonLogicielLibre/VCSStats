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
        $res = $this->_api->getRepositoryCommits("jpcomeau", "orion_equipe");
        $this->assertTrue($res == 30);
    }

    /**
     * Test getRepositoryContributors
     * @return void
     */
    public function testGetRepositoryContributors()
    {
        $res = count($this->_api->getRepositoryContributors("jpcomeau", "orion_equipe"));
        $this->assertTrue($res == 5);
    }

    /**
     * Test getRepositoryOpenPullRequests
     * @return void
     */
    public function testGetRepositoryOpenPullRequests()
    {
        $res = $this->_api->getRepositoryPullRequests("rstarnaud", "testrepo", "open");
        $this->assertTrue($res == 0);
    }

    /**
     * Test getRepositoryClosedPullRequests
     * @return void
     */
    public function testGetRepositoryClosedPullRequests()
    {
        $res = $this->_api->getRepositoryPullRequests("rstarnaud", "testrepo", "closed");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getRepositoryOpenIssues
     * @return void
     */
    public function testGetRepositoryOpenIssues()
    {
        $res = $this->_api->getRepositoryIssues("rstarnaud", "testrepo", "new");
        $this->assertTrue($res == 3);
    }

    /**
     * Test getRepositoryClosedIssues
     * @return void
     */
    public function testGetRepositoryClosedIssues()
    {
        $res = $this->_api->getRepositoryIssues("rstarnaud", "testrepo", "closed");
        $this->assertTrue($res == 1);
    }

    /**
     * Test getUserRepostitories
     * @return void
     */
    public function testGetUserRepostitories()
    {
        $res = $this->_api->getUserRepositories("rstarnaud");
        $this->assertTrue(count($res) == 1);
    }

    /**
     * Test getUserCommits
     * @return void
     */
    public function testGetUserCommits()
    {
        $res = $this->_api->getUserCommits("rstarnaud", "rstarnaud", "testrepo");
        $this->assertTrue($res == 4);
    }

    /**
     * Test getUserOpenPullRequests
     * @return void
     */
    public function testGetUserOpenPullRequests()
    {
        $res = $this->_api->getUserPullRequests("rstarnaud", "rstarnaud", "testrepo", "open");
        $this->assertTrue($res == 0);
    }

    /**
     * Test getUserClosedPullRequests
     * @return void
     */
    public function testGetUserClosedPullRequests()
    {
        $res = $this->_api->getUserPullRequests("rstarnaud", "rstarnaud", "testrepo", "closed");
        $this->assertTrue($res == 2);
    }

    /**
     * Test getUserOpenIssues
     * @return void
     */
    public function testGetUserOpenIssues()
    {
        $res = $this->_api->getUserIssues("rstarnaud", "rstarnaud", "testrepo", "new");
        $this->assertTrue($res == 3);
    }

    /**
     * Test getUserClosedIssue
     * @return void
     */
    public function testGetUserClosedIssues()
    {
        $res = $this->_api->getUserIssues("rstarnaud", "rstarnaud", "testrepo", "closed");
        $this->assertTrue($res == 1);
    }

    /**
     * Test getUserInfo
     * @return void
     */
    public function testGetUserInfo()
    {
        $res = $this->_api->getUserInfo("rstarnaud");
        $this->assertTrue(count($res) == 2);
    }
}
