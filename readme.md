# Lottery Sample App

This web app presents a list of lotteries which a user is able to participate.
The user is required to make an account using his email address and a mobile phone number. Upon entering the details an activation email is sent and the user can use the provided link to activate the account.
Once the user account is activated the user can choose one or more lotteries to participate in. When the user joins a lottery, he is automatically assigned with a unique ticket number.
When the draw date of the lottery elapses if the user has won he is notified by email and sms. If at the time of the draw the user is logged in the site, an automated animation of the current draw occurs.

## Installation
You can quickly deploy and run this using Laravel's Homestead vagrant box.
Software you need:

* Oracle VM VirtualBox `https://www.virtualbox.org/`
* Vagrant `https://www.vagrantup.com/`
* Composer `https://getcomposer.org/`
* Node.js `https://nodejs.org`
* git `https://git-scm.com/`

More information on how to install Homestead: `https://laravel.com/docs/5.2/homestead`

When you checkout the project from the repo you need to do the following things:
Starting in the project directory run:
`composer install`
