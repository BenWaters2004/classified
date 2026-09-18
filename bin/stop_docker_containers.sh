echo "STOPPING CONTAINERS"
docker stop $(docker ps -qa)
echo "DELETING CONTAINERS"
docker rm $(docker ps -qa)