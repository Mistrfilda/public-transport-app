# Public transport app

My hobby project :) I just like public transport :) Still under heavy development. 

# Features

Currently supported cities:
 - Prague - for more details about integration see my simple php wrapper for official PID api - https://github.com/Mistrfilda/pid-api
 
 Components and features:
 - List of all stops
 - Ability do download stop times (time of leave) for stop
 - Get available vehicles position
 - Departure table to stop
 - Current position of vehicles (map)
 - Statistic for vehicle lines
 - Parkings lots (capacity, location, current occupancy)
 - Current transport restrictions (view  them directly or in departure table if that affect any line)
 
# Deploy

For deploy look at https://github.com/Mistrfilda/public-transport-app-deploy - deploy is divided into second project, since my Raspberry PI runs only in my network and is not accessible from CI/CD :) 
 
# Built with
### Backend  
- PHP 7.4
- Nette 3.0 components
- Doctrine ORM
- RabbitMQ, MariaDB
- PHPStan, ECS, Nette tester

### Front
- Yarn
- Webpack (Symfony encore bundle)
- Naja.js 
- ESL lint
- Bootstrap 4 

# Configuration

config.local.neon requrired configuration

```neon
parameters:
	database:
		host: ''
		user: ''
		password: ''
		dbname: ''

	pid:
		accessToken: ''

	map:
		apiToken: ''

rabbitmq:
	connections:
		default:
			user: ''
			password: ''
			host: ''
			port: ''
```

# Available Commands

Download data for single departure table
```bash
bin/console requests:generate '{"generateDepartureTables":true,"generateVehiclePositions":false}' '{"departureTableId": "bb7266f2-f3c2-48f5-852f-4555e065c8d8"}'
```

Download stops

```bash
bin/console  prague:import:stop
```

Generate statistics

```bash
bin/console prague:statistic:generate 2
```

# RabbitMQ commands

```bash
bin/console rabbitmq:consumer pragueDepartureTableConsumer 300

bin/console rabbitmq:consumer pragueVehiclePositionConsumer 300
```

# Crons

```bash
*/2 * * * * bin/console requests:generate '{"generateDepartureTables":false,"generateVehiclePositions":true}' '{}'
5 0 * * * bin/console requests:generate '{"generateDepartureTables":true,"generateVehiclePositions":false}' '{}'
50 0 * * * bin/console prague:statistic:generate 2
10 1 * * * bin/console prague:import:stop
*/30 * * * * bin/console prague:requests:halfHour
```
