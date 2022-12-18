up:
	docker compose up || docker-compose up
down:
	docker compose down || docker-compose down
server:
	docker exec -it swoole-chat-server bash -c "composer start"
client:
	docker exec -it swoole-chat-client bash -c "php -S 127.0.0.1:3000 -t public"
