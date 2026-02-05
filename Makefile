COMPOSE_FILE=docker-compose.prod.yml

up:
	docker compose -f $(COMPOSE_FILE) up -d --build
