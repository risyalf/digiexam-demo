DEV=docker-compose.dev.yml
PROD=docker-compose.prod.yml

prod-upd:
	docker compose -f $(PROD) up -d --build

prod-down:
	docker compose -f $(PROD) down

dev-up:
	docker compose -f $(DEV) up -d --build

dev-down:
	docker compose -f $(DEV) down
