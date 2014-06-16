Github Gists PHP API
=======

Github Gists PHP API is a light-weight object Oriented wrapper for Github Gist API.

  - Built on **php5**
  - Uses **Github API v3**
  - **Easy to Use Helper Class** for updating Gists



# Demo

```php
require_once('_PATH_/gists_api.php');

$gistAPI = new gistAPI($github_ID, $github_Password);
```
```php
// Program to get a Gist.
$ouput = $gistAPI->getGist(":gist_Id");
```
```php
// Program to Edit a Gist Using The Helper Class.

$filesArray = GistEdit::init()
                ->newFile("file1.txt" => "File1 Content")
                ->newFile("file2.txt" => "File2 Content")
                ->newFile("file3.txt" => "File3 Content");
                
$ouput = $gistAPI->createGist($filesArray);
```

# Functions

Basic Usage
------------

```php
require_once('_PATH_/gists_api.php');

$gistAPI = new gistAPI(); //To Authenticate anonymous


// To Authenticate with Github Username and Password
$gistAPI = new gistAPI($github_ID, $github_Password);

```

## API Limits
```php
//Get the remainning API request limits left.
$limits = $gistAPI->getLimits();

```

## Gists

### List Gists

```php
//Display All Public Gists
$Gists = $gistAPI->listGists("public");

//Displays gists of account of username "someUsername" 
//Displays Authenticated user's gists if second atribute not given
$Gists = $gistAPI->listGists("user", ":someUsername");

//Displays gists starred by the authenticated User
$Gists = $gistAPI->listGists("starred");
```

### Get single Gist
```php
//Display a single Gist using gist's ID
$Gist = $gistAPI->getGist(":gist_id");

```

### Create a Gist
```php
//Create a new Public Gist with a Description
$filesArray = array("file1.txt" => 
                        array("content" => "Some Random Content"),
                    "file2.php" =>
                        array("content" => "<?php echo \"Content\"; ?>"),
                    );
//Create A New Public Gist
$newGist = $gistAPI->createGist($filesArray, "Some Random Description", true);

//Create A New Private Gist
$newGist = $gistAPI->createGist($filesArray, "Some Random Description", false);

// Note: $filesArray can be created in a simpler way using the Helper Class (GistEdit) explained later.
```
### Edit a Gist
```php
$filesArray = array("file1.txt" => 
                        array("content" => "Updated File Contents"),
                    "old_name.txt" =>
                        array("filename" => "new_name.txt",
                              "content"  => "Updated File Contents"),
                    "delete_this_file.txt"=> null
                    );
//Edit The Files And Description
$editGist = $gistAPI->editGist(":gist_id", $filesArray, "Some Random NEW Description"); 

//Edit Only the Files
$editGist = $gistAPI->editGist(":gist_id", $filesArray);

//Edit only the Description
$editGist = $gistAPI->editGist(":gist_id", NULL, "Some Random NEW Description"); 
```
```php
// Creating the same $fileArray above using Helper Class(Full Guide at Bottom) :
$filesArray = GistEdit::init()
                ->edit("file1.txt", "Updated File Contents")
                ->edit("old_name.txt", "Updated File Contents", "new_name.txt")
                ->deleteFile("delete_this_file.txt");
```
### List Gist Commits
```php
// Display gist's Commits
$commits = $gistAPI->gistCommits(":gist_id");
```
### Star a Gist
```php
// Star a Gist
$star = $gistAPI->starGist(":gist_id");
```
### Unstar a Gist
```php
// Unstar a Gist
$unstar = $gistAPI->unstarGist(":gist_id");
```
### Check if Gist is starred
```php
// Star a Gist
$checkStar = $gistAPI->checkStarGist(":gist_id");
```
### Fork a Gist
```php
// Fork a Gist
$fork = $gistAPI->forkGist(":gist_id");
```
### List Gist Forks
```php
// Displays all Forks of the Gist
$listForks = $gistAPI->listForkGist(":gist_id");
```
### Delete a Gist
```php
// Delete a Gist
$delete = $gistAPI->deleteGist(":gist_id");
```
## Comments

### List comments on a gist
```php
// Display all the Comments of a particular Gist
$comments = $gistAPI->gistComments(":gist_id");
```
### Get a single comment
```php
// Display a particular Comments of a particular Gist.
$comment = $gistAPI->getComment(":gist_id", ":comment_id");
```
### Create a comment
```php
// Create a new Comment in a Gist
$newComment = $gistAPI->createComment(":gist_id", "Some Random Comment Data");
```
### Edit a comment
```php
// Edit a comment of a Gist
$editComment = $gistAPI->editComment(":gist_id", ":coment_id", "New Comment Data");
```

### Delete a comment
```php
// Delete a comment of a Gist
$deleted = $gistAPI->editComment(":gist_id", ":coment_id");
```

Edit Helper Class
-------
## Basic Usage
```php
// Its Common in All Queries
$filesArray = GistEdit::init();
// Note: GistEdit being a static class. Hence NO NEED to initiate it by 
//          $GistEdit = new GistEdit();
```

### Create A New File

```php
// Add a New file to the filesArray
$filesArray = GistEdit::init()->newFile("newfilename.txt", "Some Content");
// Note: The following command can be chained with other GistEdit functions.
```
### Edit a  File

```php

// Edit a filename and its Content In the filesArray
$filesArray = GistEdit::init()->edit("filename.txt", "New Content", "new_filename.txt");

// Edit only file Content In the filesArray
$filesArray = GistEdit::init()->edit("filename.txt", "New Content");

// Edit only filename In the filesArray
$filesArray = GistEdit::init()->edit("filename.txt", NULL, "new_filename.txt");

// Note: The following command can be chained with other GistEdit functions.
```

### Delete a File

```php
// Delete a file In the filesArray
$filesArray = GistEdit::init()->deleteFile("filename.txt");
// Note: The following command can be chained with other GistEdit functions.
```