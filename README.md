# Public transport app

My hobby project :) I just like public transport :) Still under heavy development. 

# Features

Currently supported cities:
 - Prague - for more details about integration see my simple php wrapper for official PID api - https://github.com/Mistrfilda/ofce-pid-api
 
 Components and features:
 - List of all stops
 - Ability do download stop times (time of leave) for stop
 - Get available vehicles position
 - Departure table to stop
 
# Deploy

For deploy look at https://github.com/Mistrfilda/public-transport-app-deploy - deploy is divided into second project, since my Raspberry PI runs only in my network and is not accessible from CI/CD :) 
 
# Built with  
- PHP 7.3
- Nette 3.0 components
- Doctrine ORM
- RabbitMQ, MariaDB
- PHPStan, ECS, Nette tester, ESL lint
- Yarn
- Webpack (Symfony encore bundle)
- Naja.js 
- Bootstrap 4 :)