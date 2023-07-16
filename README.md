# P6_snowtricks
## Install project :
To install this project, you first need to clone it in a new directory on your computer.
Then, you can open your favorite terminal and go to the directory you just created which contain the project.
You can now install all the dependancy with the command `composer require`

## Create dataset :
Next, start your database with Xampp for example and execute the following command : `php bin/console doctrine:fixtures:load`
This will generate a dataset of post and a user for you to explore the project without having to create anything.

 ## Launch project :
To host the project on your local computer, execute : `symfony server:start`

## Known issues :
If you want to create a new user or reset the password you will have some issues :
   - First you can't send mail because the login and password for the gmail API are mine and are private.
   - Second if you want to send mail you will need to execute the command : `php bin/console messenger:consume` and choose the first option `[0] async` otherwise the mail won't send themselves.
   - Finally there is a known bug if you try to reset your password due to the project not being in production environment.
     - The url given in the mail to reset your password will miss the port 8000 which mean it will return a 404 if you click on it.
