<?php

require_once("GithubApi.php");

class GithubApiTest extends PHPUnit_Framework_TestCase
{
    private $api;

    protected function setUp()
    {
        $this->api= new GithubApi;
    }

    public function testGetRepositoryCommits()
    {
        $res = $this->api->getRepositoryCommits("raphaelstarnaud","testRepo");
        $this->assertTrue($res == 7);
    }

    public function testGetRepositoryContributors()
    {
        $res = count($this->api->getRepositoryContributors("raphaelstarnaud","testRepo"));
        $this->assertTrue($res == 1);
    }

    public function testGetRepositoryOpenPullRequests()
    {
        $res = $this->api->getRepositoryPullRequests("raphaelstarnaud","testRepo","open");
        $this->assertTrue($res == 1);
    }

    public function testGetRepositoryClosedPullRequests()
    {
        $res = $this->api->getRepositoryPullRequests("raphaelstarnaud","testRepo","closed");
        $this->assertTrue($res == 2);
    }

    public function testGetRepositoryOpenIssues()
    {
        $res = $this->api->getRepositoryIssues("raphaelstarnaud","testRepo","open");
        $this->assertTrue($res == 2);
    }

    public function testGetRepositoryClosedIssues()
    {
        $res = $this->api->getRepositoryIssues("raphaelstarnaud","testRepo","closed");
        $this->assertTrue($res == 2);
    }

    public function testGetUserRepostitories() {

        $res = $this->api->getUserRepositories("outoftime");
        $this->assertTrue(count($res) == 15);
    }

    public function testGetUserCommits() {

        $res = $this->api->getUserCommits("RaphaelStArnaud","raphaelstarnaud","testRepo");
        $this->assertTrue($res == 7);
    }

    public function testGetUserOpenPullRequests()
    {
        $res = $this->api->getUserPullRequests("RaphaelStArnaud","raphaelstarnaud","testRepo","open");
        $this->assertTrue($res == 1);
    }

    public function testUserClosedPullRequests()
    {
        $res = $this->api->getUserPullRequests("RaphaelStArnaud","raphaelstarnaud","testRepo","closed");
        $this->assertTrue($res == 2);
    }

    public function testGetUserOpenIssues()
    {
        $res = $this->api->getUserIssues("RaphaelStArnaud","raphaelstarnaud","testRepo","open");
        $this->assertTrue($res == 2);
    }

    public function testGetUserClosedIssues()
    {
        $res = $this->api->getUserIssues("RaphaelStArnaud","raphaelstarnaud","testRepo","closed");
        $this->assertTrue($res == 2);
    }

    public function testGetUserInfo()
    {
        $res = $this->api->getUserInfo("raphaelstarnaud");
        $this->assertTrue(count($res) == 3);
    }


}

?>
