container=php-fpm

up:
	docker-compose up -d

#hmmm, will build target:dependencies work properly for this?
build: docker-compose.yml 
	docker-compose rm -sf
	docker-compose down -v --remove-orphans
	docker-compose build ${container}
	#NEXT: 'make up' to start services 

vendor: app/composer.json up
	docker-compose exec ${container} composer install -q
	docker-compose exec ${container} npm install

down:
	docker-compose down

sh:
	docker-compose exec ${container} bash

tail:
	docker-compose logs -f ${container}