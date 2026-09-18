#!/bin/bash

#docker stack deploy -c docker-compose.yml stage
#docker-compose up -d --build

printf "Deploy as: \n 1) Docker Swarm \n 2) Docker Compose \n"
read choice
if [ "$choice" -eq 1 ];then
	docker stack deploy -c docker-compose.yml stage
elif [ "$choice" -eq 2 ];then
	docker-compose up -d --build
else
	printf "Error!"
fi
