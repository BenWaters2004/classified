if docker network inspect build_default | grep -q 'mailhog'; 
then
  docker compose -f docker-composeDev.yml down
else
  docker compose down
fi

