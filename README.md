# UniFi Guest Portal

[![CC-BY-4.0](https://img.shields.io/github/license/carlgo11/guest-portal?style=for-the-badge)](LICENSE)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/guest-portal/Docker%20Image%20CI?style=for-the-badge)](https://github.com/Carlgo11/guest-portal/actions)
[![GitHub Packages](https://img.shields.io/github/v/release/carlgo11/guest-portal?logo=github&style=for-the-badge)](https://github.com/Carlgo11/guest-portal/pkgs/container/guest-portal)
[![Docker Hub ](https://img.shields.io/github/v/release/carlgo11/guest-portal?logo=docker&logoColor=fff&style=for-the-badge)](https://hub.docker.com/r/carlgo11/guest-portal/tags)

## Usage

### Docker Compose

Here's a template docker-compose.yml file:

```YAML
version: "3.7"
services:

  backend:
    image: carlgo11/guest-portal:backend
    restart: unless-stopped
    read_only: true
    tmpfs:
      - /tmp
    env_file:
      - mysql.env
      - unifi.env

  frontend:
    image: carlgo11/guest-portal:frontend
    restart: unless-stopped
    user: nginx
    volumes:
      - "./bg.jpg:/guest-portal/public/img/bg/bg.jpg:ro"
    read_only: true
    tmpfs:
      - /tmp
    ports:
      - "8080:8080"

  database:
    image: linuxserver/mariadb
    restart: unless-stopped
    volumes:
      - "mysql:/var/lib/mysql"
      - "./db-template.sql:/config/initdb.d/db.sql:ro"
    environment:
      MYSQL_ROOT_PASSWORD: password
    env_file:
      - mysql.env

volumes:
  mysql:
```

### Background images

The site fetches `bg.jpg` from `/guest-portal/public/img/bg/` in the Docker container and converts it into JPEG, AV1 and
WebP images in different resolutions.

Link to an appropriate image (preferably larger than 1920x1080) as `bg.jpg` before starting the `frontend` container. In
the example `docker-compose.yml` above, `bg.jpg` is placed in the same directory as the Docker compose file.

### Environment variables

| Name           | Default  | Description                    |          Example           |
|:---------------|:--------:|:-------------------------------|:--------------------------:|
| MYSQL_HOST     | database | MySQL server url               |     database/127.0.0.1     |
| MYSQL_PORT     |   3306   | MySQL server port              |            3306            |
| MYSQL_USER     |          | MySQL username                 |        guest-portal        |
| MYSQL_PASSWORD |          | MySQL password                 |          password          |
| MYSQL_DATABASE |          | MySQL database name            |        guest-portal        |
| UNIFI_USER     |          | UniFi Hotspot username         |        guest-portal        |
| UNIFI_PASSWORD |          | UniFi Hotspot password         |          password          |
| UNIFI_URL      |          | UniFi Controller IP/URL & port | <https://192.168.1.2:8443> |
| UNIFI_SITE     | default  | UniFi Site                     |          default           |
| UNIFI_VERSION  |  6.0.0   | Controller version             |           6.0.44           |
| LANG           |    en    | Language pack to use           |             en             |
| DATABASE       |          | Storage method. (MySQL/Redis)  |           mysql            |

## Example portal showcase

<div>
<img src="https://user-images.githubusercontent.com/3535780/89343900-4b548680-d6a5-11ea-8896-f39486b21102.jpg" width="49.5%">
<img src="https://user-images.githubusercontent.com/3535780/89343904-4d1e4a00-d6a5-11ea-9776-434ad01a2ac5.jpg" width="49.5%">
</div>
<div>
<img src="https://user-images.githubusercontent.com/3535780/89343907-4e4f7700-d6a5-11ea-9510-521495fb0226.png" width="49.5%">
<img src="https://user-images.githubusercontent.com/3535780/89343905-4d1e4a00-d6a5-11ea-95de-d2edaaebe4aa.png" width="49.5%">
</div>

## License

This work is licensed under the GPLv3.  
To view a copy of this license, visit [LICENSE](LICENSE).
