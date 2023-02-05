# UniFi Guest Portal

[![CC-BY-4.0](https://img.shields.io/github/license/carlgo11/guest-portal?style=for-the-badge)](LICENSE)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/guest-portal/Docker%20Image%20CI?style=for-the-badge)](https://github.com/Carlgo11/guest-portal/actions)
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

  frontend:
    image: carlgo11/guest-portal:frontend
    restart: unless-stopped
    user: nginx
    volumes:
      - "./bg.jpg:/guest-portal/public/img/bg/q1/bg.jpg:ro"
      - "./bg.jpg:/guest-portal/public/img/bg/q2/bg.jpg:ro"
      - "./bg.jpg:/guest-portal/public/img/bg/q3/bg.jpg:ro"
      - "./bg.jpg:/guest-portal/public/img/bg/q4/bg.jpg:ro"
    read_only: true
    tmpfs:
      - /tmp
      - /guest-portal/public/img/bg/
    ports:
      - "8080:8080"
    env_file:
      - unifi.env

  mysql:
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

The frontend service requires background images to be present.
In `/guest-portal/public/img/bg/` there should be a directory for each quarter of the year. `q1`,`q2`,`q3`,`q4`.
These directories are used to host background images.

The site fetches `bg.jpg` from `/guest-portal/public/img/bg/q{1,2,3,4}` in the Docker container and converts it into
JPEG, AV1 and
WebP images in different resolutions.

Link to an appropriate image (preferably larger than 1920x1080) as `bg.jpg` before starting the `frontend` container. In
the example `docker-compose.yml` above, `bg.jpg` is placed in the same directory as the Docker compose file.

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
| UNIFI_SITE     | default | UniFi Site                     |        default        | Backend, Frontend |
| UNIFI_VERSION  |  6.0.0  | Controller version             |        6.0.44         | Backend           |
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
