# UniFi Guest Portal

[![CC-BY-4.0](https://img.shields.io/github/license/carlgo11/guest-portal?style=for-the-badge)](LICENSE)
[![GitHub Packages](https://img.shields.io/github/v/release/carlgo11/guest-portal?logo=github&style=for-the-badge)](https://github.com/Carlgo11/guest-portal/pkgs/container/guest-portal)
[![Docker Hub ](https://img.shields.io/github/v/release/carlgo11/guest-portal?logo=docker&logoColor=fff&style=for-the-badge)](https://hub.docker.com/r/carlgo11/guest-portal/tags)

External UniFi Guest Portal using PHP & MariaDB, packaged to be run through Docker.

## Installation

### Requirements

To run a guest portal you'll need:

1. A UniFi controller
2. A server reachable by the guest network.
3. Docker & Docker-Compose

### Docker Compose

The project is split into three different services:

* The frontend, responsible for the static website content.
* The backend, responsible for dynamic website content.
* The database, storing vouchers and admin credentials.

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
      networks:
         - php
         - mysql

   frontend:
      image: carlgo11/guest-portal:frontend
      restart: unless-stopped
      volumes:
         - "./images/:/guest-portal/public/img/bg/:ro"
      read_only: true
      tmpfs:
         - /tmp
      ports:
         - "8080:8080"
      networks:
         - php

   database:
      image: linuxserver/mariadb:latest
      volumes:
         - "mysql:/var/lib/mysql"
         - "./resources/db-template.sql:/config/initdb.d/db.sql"
      env_file:
         - mysql.env
      networks:
         - mysql

networks:
   mysql:
   php:
volumes:
   mysql:
```

### Background images

The frontend service requires background images to be present in `/guest-portal/public/img/bg/`.
The following images are to be present in the folder:

| File         | Type | Recommended resolution |
|--------------|------|------------------------|
| `bg.jpg`     | JPEG | 4K                     |
| `bg.avif`    | AV1  | 4K                     |
| `bg.webp`    | WEBP | 4K                     |
| `bg-lg.jpg`  | JPEG | 1920w                  |
| `bg-lg.avif` | AV1  | 1920w                  |
| `bg-lg.webp` | WEBP | 1920w                  |
| `bg-md.jpg`  | JPEG | 768w                   |
| `bg-md.avif` | AV1  | 768w                   |
| `bg-md.webp` | WEBP | 768w                   |
| `bg-sm.jpg`  | JPEG | 576w                   |
| `bg-sm.avif` | AV1  | 576w                   |
| `bg-sm.webp` | WEBP | 576w                   |

#### Seasonal/Quarterly backgrounds

If you want to present different background images based on the current season, set the environment variable `BG_SEASONAL` to 1 on the backend server.
You should then create four new directories in the `/guest-portal/public/img/bg/` directory:

| Directory                         | Active dates               |
|-----------------------------------|----------------------------|
| `/guest-portal/public/img/bg/q1/` | March 20 - June 19         |
| `/guest-portal/public/img/bg/q2/` | June 20 - September 21     |
| `/guest-portal/public/img/bg/q3/` | September 22 - December 20 |
| `/guest-portal/public/img/bg/q4/` | December 21 - March 19     |

Inside each directory, you should place images corresponding to background image table above. 

```YAML
volumes:
   - "./images/q1/:/guest-portal/public/img/bg/q1/:ro"
   - "./images/q2/:/guest-portal/public/img/bg/q2/:ro"
   - "./images/q3/:/guest-portal/public/img/bg/q3/:ro"
   - "./images/q4/:/guest-portal/public/img/bg/q4/:ro"
environment:
   BG_SEASONAL: "1"
```

### Environment variables

| Name           | Default | Description                    |        Example        | Used By           |
|:---------------|:-------:|:-------------------------------|:---------------------:|:------------------|
| MYSQL_HOST     |  mysql  | MySQL server hostname/IP       |    mysql/127.0.0.1    | Backend, MySQL    |
| MYSQL_PORT     |  3306   | MySQL server port              |         3306          | Backend, MySQL    |
| MYSQL_USER     |         | MySQL username                 |     guest-portal      | Backend, MySQL    |
| MYSQL_PASSWORD |         | MySQL password                 |       password        | Backend, MySQL    |
| MYSQL_DATABASE |         | MySQL database name            |     guest-portal      | Backend, MySQL    |
| UNIFI_USER     |         | UniFi Hotspot username         |     guest-portal      | Backend           |
| UNIFI_PASSWORD |         | UniFi Hotspot password         |       password        | Backend           |
| UNIFI_URL      |         | UniFi Controller IP/URL & port | <https://192.168.1.2> | Backend           |
| UNIFI_VERSION  |  7.0.0  | UniFi Controller version       |        7.3.89         | Backend           |
| LANG           |   en    | Language pack to use           |          en           | Backend           |
| DATABASE       |  mysql  | Storage method. (MySQL/Redis)  |         mysql         | Backend           |

## Usage

### Voucher

1. **Admin**
    1. Go to `http(s)://(guest portal url)/admin` and enter your credentials.
    2. Create new voucher.
2. **User**
    1. Select the guest Wi-Fi.
    2. Wait for the guest portal to show up-
    3. Enter the voucher.

### Manual Approval

1. **User**
    1. Select the guest Wi-Fi.
    2. Click "Manual approval".
    3. Wait for admin to authorize the device.
2. **Admin**
    1. Open the UniFi Network portal via web or the app.
    2. Find the user's device.
    3. Click on 'Authorize'.

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
