## Simple Swoole Chat
![](/.github/images/screenshot.png)

## Requirements

- PHP >= 8.1
- Composer
- Docker
- Make (optional)

## Installation

```bash
composer install
```

## Running

**With Docker**

```bash
> docker compose up || docker-compose up
> docker exec -it swoole-chat-server bash -c "composer start"
```

**With Make**

```bash
> make up
> make server
```

And open `public/index.html` file on browser.
