up:
	docker compose up || docker-compose up
down:
	docker compose down || docker-compose down
server:
	docker exec -it swoole-chat-server bash -c "composer start"
