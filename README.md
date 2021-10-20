# UniFi Guest Portal

[![CC-BY-4.0](https://img.shields.io/github/license/carlgo11/guest-portal?style=for-the-badge)](LICENSE)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/guest-portal/Docker%20Image%20CI?style=for-the-badge)](https://github.com/Carlgo11/guest-portal/actions)
[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/carlgo11/guest-portal?logo=github&style=for-the-badge)](https://github.com/Carlgo11/guest-portal/releases/latest)
[![Docker](https://img.shields.io/badge/Docker-Download-2496ed?style=for-the-badge&logo=docker&logoColor=fff)](https://hub.docker.com/r/carlgo11/guest-portal/)

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
      - "./resources/nginx.conf:/etc/nginx/nginx.conf"
      - "./resources/images/:/guest-portal/public/img/bg/"
    read_only: true
    tmpfs:
      - /tmp
    ports:
      - "8080:8080"

  database:
    image: linuxserver/mariadb:alpine
    restart: unless-stopped
    volumes:
      - "mysql:/var/lib/mysql"
      - "./resources/db-template.sql:/config/initdb.d/db.sql:ro"
    environment:
      MYSQL_ROOT_PASSWORD: password
    env_file:
      - mysql.env

volumes:
  mysql:
```

### Environment variables

|Name|Default|Description|Example|
|----|-------|-----------|-------|
|MYSQL_HOST| |MySQL server url|database.docker/127.0.0.1
|MYSQL_PORT| |MySQL server port|3306
|MYSQL_USER| |MySQL username|guest-portal
|MYSQL_PASSWORD| |MySQL password|password
|MYSQL_DATABASE| |MySQL database name|guest-portal
|UNIFI_USER| |UniFi Hotspot username|guest-portal
|UNIFI_PASSWORD| |UniFi Hotspot password|password
|UNIFI_URL| |UniFi Controller IP/URL & port|<https://192.168.1.2:8443>
|UNIFI_SITE|default|UniFi Site|default
|UNIFI_VERSION| |Controller version|5.13.32

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
