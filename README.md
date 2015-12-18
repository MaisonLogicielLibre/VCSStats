# API_SVN

Use as plugin only, wont work without being in a vendor software and autoload file generated

## Nomenclature

getRepository% : These functions means that it will get the objects for the whole repository

getUser% : These functions means that it will get the objects for the specific user

$owner : The organization/owner of the repository

$repo : The reposiotry name

$state : the state of the object : open, closed, all

$since : Start date for filter

$until : End date for fitler

$user : The username of the user

## GithubAPI

## Functions

getRepository% : These functions can be filtered by start and end date (both required)

getUser% : These functions cannot be filtered by start and end date

getUserRepositories : This function use Google BigData table : GitHubArchive. This function require the ML2 google project to have a                        sufficient bandwith allocation to be able to run the query through the entire table to find the specific user.                        It is possible that the specifix user is not yet in the table as the table does not update isntantly.

### Get the data for ML2 website

To be able to update the statistics, you need to add this code to the statistics function in PagesController :

$api = new GithubApi();
$since = '2015-12-14T00:00:00Z';
$until = '2015-12-21T00:00:00Z';

$commits = $api->getRepositoryCommits('MaisonLogicielLibre', 'Website', $since, $until);
$prs = $api->getRepositoryPullRequests('MaisonLogicielLibre', 'Website', 'all', $since, $until);
$iss = $api->getRepositoryIssues('MaisonLogicielLibre', 'Website', 'all', $since, $until);

var_dump(count($commits));
var_dump(count($prs[0]));
var_dump(count($prs[1]));
var_dump(count($iss[0]));
var_dump(count($isss));

die;
