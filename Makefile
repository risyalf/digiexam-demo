DEV=docker-compose.dev.yml
PROD=docker-compose.prod.yml

prod-upd:
	podman-compose -f $(PROD) up -d --build

prod-down:
	podman-compose -f $(PROD) down

dev-up:
	podman-compose -f $(DEV) up -d --build

dev-down:
	podman-compose -f $(DEV) down
