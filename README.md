# UniFi Guest Portal

[![CC-BY-4.0](https://img.shields.io/github/license/carlgo11/guest-portal?style=for-the-badge)](LICENSE)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Carlgo11/guest-portal/Docker%20Image%20CI?style=for-the-badge)](https://github.com/Carlgo11/guest-portal/actions)
[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/carlgo11/guest-portal?logo=github&style=for-the-badge)](https://github.com/Carlgo11/guest-portal/releases/latest)
[![Docker](https://img.shields.io/badge/Docker-Download-2496ed?style=for-the-badge&logo=docker&logoColor=fff)](https://hub.docker.com/r/carlgo11/guest-portal/)

## Usage

```BASH
docker run -p 80:80 carlgo11/guest-portal
```

Optionally, add some background images:

```BASH
docker run -p 80:80 -v $(pwd)/bg.webp:/opt/www/img/bg.webp -v $(pwd)/bg.jpg:/opt/www/img/bg.jpg carlgo11/guest-portal
```

### Environment variables

|Name|Default|Description|Example|
|----|-------|-----------|-------|
|UNIFI_USER| |UniFi Hotspot username|guest-portal
|UNIFI_PASSWORD| |UniFi Hotspot password|password
|UNIFI_URL| |UniFi Controller IP/URL & port|<https://192.168.1.2:8443>
|UNIFI_SITE|default|UniFi Site|default
|UNIFI_VERSION| |Controller version|5.13.32

### Docker Compose

Here's a template docker-compose.yml file:

```YAML
version: '3.1'
services:
  guest-portal:
    image: carlgo11/guest-portal
    ports:
      - 8080:80
    environment:
      - UNIFI_USER=guest-portal
      - UNIFI_PASSWORD=abc123
      - UNIFI_URL=https://192.168.1.2:8443
      - UNIFI_SITE=default
      - UNIFI_VERSION=5.13.32
    volumes:
      - ./bg.jpg:/opt/www/img/bg.jpg
      - ./bg.webp:/opt/www/img/bg.webp
    restart: on-failure
```

## License

This work is licensed under the Creative Commons Attribution 4.0 International License.  
To view a copy of this license, visit [LICENSE](LICENSE).
