# Lottery Sample App

This web app is a simple lottery system in which someone can participate in one or more of them. 
It presents a list of lotteries which a user is able to participate.
The user is required to make an account using his email address and a mobile phone number. Upon entering the details an activation email is sent and the user can use the provided link to activate the account.
Once the user account is activated the user can choose one or more lotteries to participate in. When the user joins a lottery, he is automatically assigned with a unique ticket number.
When the draw date of the lottery elapses if the user has won he is notified by email and sms. If at the time of the draw the user is logged in the site, an automated animation of the current draw occurs.

## Installation
You can quickly deploy and run this using Laravel's Homestead vagrant box.
Software you need:

* Oracle VM VirtualBox `https://www.virtualbox.org/`
* Vagrant `https://www.vagrantup.com/`
* PHP >= 5.5.9 `https://www.php.net/`
* Composer `https://getcomposer.org/`
* Node.js `https://nodejs.org`
* git `https://git-scm.com/`

More information on how to install Homestead: `https://laravel.com/docs/5.2/homestead`

When you checkout the project from the repo you need to do the following things:
Starting in the project directory run:
`copy Homestead.yaml.example Homestead.yaml`
then edit `Homestead.yaml` and fill your local/prefered settings, then
```
composer install
cd public
npm install
cd ..\_lottery_draw
npm install
```

after that you are ready to run the vagrant box by typing in the project dir:
`vagrant up`

you also need to configure your local environment
copy `.env.example` file to `.env` and then edit it to your local config.

The node.js app within the `_lottery_draw` folder gets it's settings from the project's `.env` file. This small app is required to run for the project to work correctly.

You can ssh in to the vagrant box and run the node app from there.



